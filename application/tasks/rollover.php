<?php


class Rollover_Task {

	protected static $to_unset = array('id','created_by', 'live', 'locked_to', 'current_revision', 'live_revision');

	/**
	 * Run the seed command.
	 * 
	 * @param array  $arguments The arguments sent to the seed command.
	 */
	public function run($arguments = array())
	{
		echo "Please select a type: rollover:ug 2014 2015 \n";
	}

	public function ug($arguments = array())
	{

		if(sizeof($arguments) != 2) die("Please provide a from and to year. \n");

		Auth::login(1);
		
		$from_year = $arguments[0];
		$to_year = $arguments[1];	

		foreach(UG_Programme::where('year', '=', $from_year)->get() as $programme){

			$attributes = $programme->attributes;

			foreach(static::$to_unset as $unset){
				unset($attributes[$unset]);
			}

			$copy = new UG_Programme;
			$copy->fill($attributes);

			$copy->year = $to_year;
			$copy->save();
		}

		die("Rollover complete. \n");

	}
	public function pg($arguments = array())
	{
		if(sizeof($arguments) != 2) die("Please provide a from and to year. \n");

		Auth::login(1);

		$from_year = $arguments[0];
		$to_year = $arguments[1];

		foreach(PG_Programme::where('year', '=', $from_year)->get() as $programme){

			$attributes = $programme->attributes;

			foreach(static::$to_unset as $unset){
				unset($attributes[$unset]);
			}

			$copy = new PG_Programme;
			$copy->fill($attributes);

			$copy->year = $to_year;
			$copy->save();
		}

		die("Rollover complete. \n");
	}

}