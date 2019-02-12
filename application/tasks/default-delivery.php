<?php

class Default_Delivery_Task
{
	static $AWARD_RANKINGS = array(
		'phd' => 12,
		'sportd' => 11,
		'engd' => 10,
		'mphil' => 9,
		'mba' => 8,
		'llm' => 7,
		'msc' => 6,
		'march' => 5,
		'ma' => 4,
		'pdip' => 3,
		'pcert' => 2,
		'gdip' => 1,
	);

	public function run($arguments = array())
	{
		$arguments = $this->parseArguments($arguments);
		$programmes = PG_Programme::with('deliveries.award')
			->where('year', '=', $arguments['year'])
			->where('hidden', '=', 0)
			->order_by('programme_title_1')->get();
		foreach ($programmes as $programme) {
			$deliveries = $programme->deliveries;
			$default_delivery = $programme->getDefaultDelivery($deliveries);
			$preferred_delivery = null;
			$preferred_delivery = $programme->getPreferredDelivery($deliveries);
			echo $programme->programme_title_1 . "\n";
			foreach ($deliveries as $delivery) {
				$is_default = ($default_delivery && $delivery->id == $default_delivery->id) ? '* ' : '';
				$is_preferred = ($preferred_delivery && $delivery->id == $preferred_delivery->id) ? '+' : '';
				echo "$is_preferred\t$is_default$delivery->description\n";
			}
		}
	}

	private function parseArguments($arguments = array())
	{
		$parsedArguments = array();

		foreach ($arguments as $argument) {
			$argumentPair = explode('=', $argument);
			if (count($argumentPair) == 2) {
				$parsedArguments[$argumentPair[0]] = $argumentPair[1];
			}
			else{
				$parsedArguments[$argument] = $argument;
			}
		}

		if (!isset($parsedArguments['year'])) {
			self::printUsage();
		}

		if (!is_numeric($parsedArguments['year']) || strlen($parsedArguments['year']) !== 4) {
			self::printUsage();
		}
		return $parsedArguments;
	}

	/**
	 * Sets the default award and attendance pattern. Default options will only set defaults if no default is already set.
	 * php artisan default-delivery:set year=<year> [filter=<filter-text>] [ids=<comma,separated,programme,ids] [-f] [-d] [-c]
	 * @param array $arguments - key => value arguments
	 * - year is required
	 * - -d - just outputs the result without doing anything
	 * - -f - overwrite existing values
	 * - -c - clear the mcr value if it is set.
	 */
	public function set($arguments = array())
	{
		$mcr_field = PG_Programme::get_display_course_structure_mcr_field();
		$award_field = PG_Programme::get_display_course_structure_award_field();
		$pattern_field = PG_Programme::get_display_course_structure_attendance_pattern_field();

		$arguments = $this->parseArguments($arguments);
		$force = isset($arguments['-f']);
		$dry = isset($arguments['-d']);
		$clear_mcr = isset($arguments['-c']);
		$ids = array();
		foreach(isset($arguments['ids']) ? explode(',',$arguments['ids']) : array() as $id) {
			settype($id,'integer');
			if($id) {
				$ids[] = $id;
			}
		}
		$filter = !empty($arguments['filter']) ? $arguments['filter'] : '';
		$programmes = PG_Programme::with('deliveries.award')
			->where('year', '=', $arguments['year'])
			->where('hidden', '=', 0)
			->order_by('programme_title_1');
		if($ids) {
			$programmes = $programmes->where_in('instance_id',$ids);
		}
		if($filter) {
			$programmes->where('programme_title_1','like',"%$filter%");
		}
		$programmes = $programmes->get();
		foreach ($programmes as $programme) {
			$deliveries = $programme->deliveries;
			$default_delivery = $programme->getDefaultDelivery($deliveries);
			$preferred_delivery = $programme->getPreferredDelivery($deliveries);
			if(!$preferred_delivery || $force) {
				$programme->$award_field = $default_delivery && $default_delivery->relatedaward ? $default_delivery->relatedaward->name : '';
				$programme->$pattern_field = $default_delivery ? $default_delivery->attendance_pattern : '';
			}
			if($clear_mcr) {
				$programme->$mcr_field = '';
			}
			if(!$dry) {
				$this->savePreferredDelivery($programme);
			}
			echo $programme->programme_title_1 . " - " . $programme->instance_id . "\n";
			foreach ($deliveries as $delivery) {
				$is_default = ($default_delivery && $delivery->id == $default_delivery->id) ? '* ' : '';
				$is_preferred = ($preferred_delivery && $delivery->id == $preferred_delivery->id) ? '+' : '';
				echo "$is_preferred\t$is_default$delivery->description\n";
			}
		}
		if(!$dry){
			// from Revisionable::make_revision_live()
			API::purge_output_cache();
		}
	}

	/**
	 * Updates the programmes_pg and programmes_revisions_pg tables with the specified preferred delivery info for
	 * a program.
	 * We update all revisions of the programme in the year we are updating to avoid dealing with whether or not the
	 * program / current revision is published etc as this is a (more-or-less) one-off task.
	 * Finally we need to clear the cache for this programme...
	 * ... oh wait do we need to regenerate a cache afterwards?
	 * @param $programme
	 */
	private function savePreferredDelivery(PG_Programme $programme)
	{
		// get field names
		$mcr_field = PG_Programme::get_display_course_structure_mcr_field();
		$award_field = PG_Programme::get_display_course_structure_award_field();
		$pattern_field = PG_Programme::get_display_course_structure_attendance_pattern_field();
		$updates = array(
			$mcr_field => $programme->$mcr_field,
			$award_field => $programme->$award_field,
			$pattern_field => $programme->$pattern_field
		);
		DB::table(PG_Programme::$table)
			->where('id', '=', $programme->id)
			->update($updates);
		$revision_model = PG_Programme::$revision_model;
		DB::table($revision_model::$table)
			->where('programme_id','=',$programme->id)
			->update($updates);

		// from simpledata::save()
		PG_Programme::clear_all_as_list_cache($programme->year);
		// from Programme::generate_api_data() called by Programme::make_revision_live()
		PG_Programme::generate_api_programme($programme->instance_id, $programme->year);
		PG_Programme::generate_api_index($programme->year);
	}

	private function printUsage()
	{
		echo 'usage:

// to see the current default delivery and preferred delivery for all courses for a particular year
php artisan default-delivery year=<year>

// to set the defaults use:
php artisan default-delivery:set year=<year> [filter=<filter-text>] [ids=<comma,separated,programme,ids] [-f] [-d] [-c]
- year is required
- filter=<filter-text> - only affect programmes whose title matches the filter
- ids=<programme_ids> - only affect programmes whose ids are in the comma separated list.
- -d - dry run - just outputs the result without doing anything
- -f - force - overwrite existing values
- -c - clear the mcr value if it is set.
';
		die();
	}

	public function help()
	{
		$this->printUsage();
	}
}
