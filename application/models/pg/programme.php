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

}