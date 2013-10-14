<?php

ini_set('max_execution_time', 600);

class Module_Session_Task {

	/**
	 * run
	 */
	public function run($arguments = array())
	{
		Auth::login(1);
		$programmes = UG_Programme::where('year', '=', '2014')->get();
		foreach ($programmes as $programme)
		{
			$live_id = $programme->live_revision;
			$current_id = $programme->current_revision;
			$live_revision = $programme->find_live_revision();
			if ( $live_revision && ($live_id == $current_id) )
			{
				$programme->module_session_86 = '';
				$programme->save();
				$new_current_id = $programme->current_revision;
				$programme->make_revision_live((int) $new_current_id);
			}
		}
	}


}