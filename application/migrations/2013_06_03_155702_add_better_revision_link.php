<?php

class Add_Better_Revision_Link {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes', function($table){
			$table->integer('live_revision');
			$table->integer('current_revision');
		});

		// Setup inital linkage between programmes and revisions
		foreach(Programme::all() as $programme){

			// Dont use model functions as these will change
			// Get current
			if ($programme->live == 2)
			{
				$current =  ProgrammeRevision::where('programme_id','=',$programme->id)->where('year','=',$programme->year)->where('status','=','live')->first(array('id'));
			}
			else
			{
				$current = ProgrammeRevision::where('programme_id', '=' , $programme->id)->where('year','=',$programme->year)->where('status', '=', 'selected')->first(array('id'));
			}
			// If no current, record is in invalid state - attempt fix by using latest as current
			if($current == null){
				$current = ProgrammeRevision::where('programme_id', '=' , $programme->id)->where('year','=',$programme->year)->order_by('id', 'desc')->first(array('id'));
			}
			$current = $current->id;

			$live = 0;
			// Get live
			if($programme->live != 0){
				$live = ProgrammeRevision::where('programme_id', '=' , $programme->id)->where('year','=',$programme->year)->where('status','=','live')->first(array('id'));
				$live = ($live !== null) ? $live->id : 0;
			}

			$programme->live_revision = $live;
			$programme->current_revision = $current;
			$programme->raw_save();
		}

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programmes', function($table){
			$table->drop_column('live_revision');
			$table->drop_column('current_revision');
		});
	}

}