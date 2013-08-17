<?php

class PG_Programme extends Programme {
	public static $table = 'programmes_pg';
	public static $revision_model = 'PG_ProgrammeRevision';
	public static $type = 'pg';

	// Get deliveries for this programme
	public function get_deliveries()
	{
		return PG_Deliveries::where('programme_id','=',$this->id)->get();
	}

	public function deliveries()
	{
	  	return $this->has_many('pg_deliveries', 'programme_id');
	}

	/**
	 * Get this programme's awards.
	 * 
	 * @return Award The award for this programme.
	 */
	public function awards()
	{
		$award_field = static::get_award_field();
		$ids = explode(',', $this->$award_field);
		return PG_Award::where_in('id', $ids)->get();
	}

	/**
	 * Get a list of this programme's award names.
	 * 
	 * @return string A comma seperated string of awards.
	 */
	public function get_award_names()
	{
		$awards = $this->awards();
		$award_string = '';
		$count = 0;

		foreach ($awards as $award) {
			$award_string .= (($count > 0) ? ', ' : '') . $award->name;
			$count++;
		}

		return $award_string;
	}

}