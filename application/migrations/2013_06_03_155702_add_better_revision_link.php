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
		Schema::table('programme_settings', function($table){
			$table->integer('live_revision');
			$table->integer('current_revision');
		});
		Schema::table('global_settings', function($table){
			$table->integer('live_revision');
			$table->integer('current_revision');
		});

		// Setup inital linkage between programmes and revisions
		foreach(DB::table('programmes')->get() as $programme){

			// Dont use model functions as these will change
			// Get current
			if ($programme->live == 2)
			{
				$current =  DB::table('programmes_revisions')->where('programme_id','=',$programme->id)->where('year','=',$programme->year)->where('status','=','live')->first(array('id'));
			}
			else
			{
				$current = DB::table('programmes_revisions')->where('programme_id', '=' , $programme->id)->where('year','=',$programme->year)->where('status', '=', 'selected')->first(array('id'));
			}
			// If no current, record is in invalid state - attempt fix by using latest as current
			if($current == null){
				$current = DB::table('programmes_revisions')->where('programme_id', '=' , $programme->id)->where('year','=',$programme->year)->order_by('id', 'desc')->first(array('id'));
			}
			$current = $current->id;

			$live = 0;
			// Get live
			if($programme->live != 0){
				$live = DB::table('programmes_revisions')->where('programme_id', '=' , $programme->id)->where('year','=',$programme->year)->where('status','=','live')->first(array('id'));
				$live = ($live !== null) ? $live->id : 0;
			}

			DB::table('programmes')
				->where('id', '=', $programme->id)
				->update(array(
					'live_revision'  => $live,
					'current_revision' => $current
			));

		}
		// migrate other datatypes
		foreach(DB::table('programme_settings')->get() as $setting){
			$live_setting = DB::table('programme_settings_revisions')->where('year','=',$setting->year)->where('status','=','live')->first(array('id'));
			
			DB::table('programme_settings')
				->where('id', '=', $setting->id)
				->update(array(
					'live_revision'  => $live_setting->id,
					'current_revision' => $live_setting->id
			));
		}
		foreach(DB::table('global_settings')->get() as $global){
			$live_setting = DB::table('global_settings_revisions')->where('year','=',$setting->year)->where('status','=','live')->first(array('id'));
			
			DB::table('global_settings')
				->where('id', '=', $global->id)
				->update(array(
					'live_revision'  => $live_setting->id,
					'current_revision' => $live_setting->id
			));
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
		Schema::table('programme_settings', function($table){
			$table->drop_column('live_revision');
			$table->drop_column('current_revision');
		});
		Schema::table('global_settings', function($table){
			$table->drop_column('live_revision');
			$table->drop_column('current_revision');
		});
	}

}