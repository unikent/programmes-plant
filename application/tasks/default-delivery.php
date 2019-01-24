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
			->where('year','=',$arguments['year'])
			->where('hidden','=',0)
			->order_by('programme_title_1')->get();
		foreach($programmes as $programme) {
			$deliveries = $programme->deliveries;
			$default_delivery = $programme->getDefaultDelivery($deliveries);
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
		}

		if(!isset($parsedArguments['year'])) {
			self::printUsage();
		}

		if (!is_numeric($parsedArguments['year']) || strlen($parsedArguments['year']) !== 4) {
			self::printUsage();
		}
		return $parsedArguments;
	}

	private function printUsage()
	{
		echo 'usage:

// to see the current default delivery for all courses for a particular year
php artisan default-delivery year=<year>

';

		die();
	}

	public function help()
	{
		$this->printUsage();
	}
}