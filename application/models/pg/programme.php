<?php

class PG_Programme extends Programme {
	public static $table = 'programmes_pg';
	public static $revision_model = 'PG_ProgrammeRevision';
	public static $type = 'pg';

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

	/**
	 * Gets the delivery which has been set as the preferred delivery.
	 * @param array|null $deliveries - Array of deliveries to search in OR null, in which case all deliveries for this program are used.
	 * @return PG_Delivery|null - The delivery or null if no preferred delivery is set.
	 */
	public function getPreferredDelivery($deliveries = null)
	{
		$deliveries = (null === $deliveries) ? $this->deliveries()->with('award')->get() : $deliveries;
		$mcr_field = static::get_display_course_structure_mcr_field();
		$award_field = static::get_display_course_structure_award_field();
		$pattern_field = static::get_display_course_structure_attendance_pattern_field();
		foreach($deliveries as $delivery) {
			if($this->$mcr_field) {
				if($delivery->mcr === $this->$mcr_field) {
					return $delivery;
				}
			}
			else {
				$award = $delivery->award()->first();
				if($award && $award->name === $this->$award_field
				&& $this->$pattern_field === $delivery->attendance_pattern) {
					return $delivery;
				}
			}
		}
		return null;
	}

	/**
	 * Gets the delivery that would be the default if not overridden (regardless of whether one has been set yet).
	 * @param array|null $deliveries - Array of deliveries to search in OR null, in which case all deliveries for this program are used.
	 * @return PG_Delivery|null - The delivery that would be preferred by default or null if none exists.
	 */
	public function getDefaultDelivery($deliveries = null)
	{
		$default = null;
		$deliveries = (null === $deliveries) ? $this->deliveries()->with('award')->get() : $deliveries;
		$n_deliveries = is_array($deliveries) ? count($deliveries) : 0;
		if($n_deliveries) {
			$default = $deliveries[0];
			$award_obj = $default->award()->first();
			$award = $award_obj ? strtolower($award_obj->name) : '';
			$top_ranking = isset(self::$AWARD_RANKINGS[$award]) ? self::$AWARD_RANKINGS[$award] : 0;
			for ($i = 1; $i < $n_deliveries; $i++) {
				$award_obj = $deliveries[$i]->award()->first();
				$award = $award_obj ? strtolower($award_obj->name) : '';
				$ranking = isset(self::$AWARD_RANKINGS[$award]) ? self::$AWARD_RANKINGS[$award] : 0;
				if($ranking > $top_ranking) {
					$default = $deliveries[$i];
					$top_ranking = $ranking;
				}
			}
		}
		return $default;
	}


}