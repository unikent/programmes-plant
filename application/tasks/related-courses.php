<?php

/*
	2018-07-10 
	this is a one off task to update the IDs used for the related courses 

	Situation was that the IDs which has been saved for the related courses were the course ids rather than their instance ids
	done as part of https://github.com/unikent/programmes-plant/pull/959
	
	usage:
	
	// to see the current state of all the courses and their related courses
	php artisan related-courses:view 
	
	// to update the related courses so that they are related by instance id rather than id
	php artisan related-courses:update
	
	// @TODO - saving the programmes in the update function is not working yet!
	
	See Christian for more details if needed

*/
class Related_Courses_Task {
	
	/**
	 * getProgramme - gets a programme
	 *
	 * @param int 	$id	
	 * @param string level - 'ug' or 'pg'
	 * @return stdClass - the programme
	 */
	private function getProgramme($id, $level = 'ug')
	{
		$programmes_table = "programmes_$level";
		$programmes_model = strtoupper($level) . '_Programme';
		$programme_title_field = $programmes_model::get_programme_title_field();
		$programme = DB::table($programmes_table)->where('id', '=', $id)->first();

		return $programme;
	}

	/**
	 * replaces the related courses for a given programme at a given level 
	 *
	 * @param mixed 	$programme - the programme to update
	 * @param string 	level - 'ug' or 'pg'
	 * @param array 	$updatedRelatedCoursesIDsArray - instance IDs for courses to relate tp $programme
	 * @return void
	 */
	private function updateRelatedProgrammes($programme, $level, $updatedRelatedCoursesIDsArray)
	{
		if (0 === count($updatedRelatedCoursesIDsArray)) {
			return;
		}

		// the original data began with a ',' so I'm keeping that here 
		$updatedRelatedCoursesIDs = ',' . implode(',', $updatedRelatedCoursesIDsArray);

		// figure out database cols
		$programmes_table = "programmes_$level";
		$programmes_model = strtoupper($level) . '_Programme';
		$related_courses_field = $programmes_model::get_related_courses_field();
		
		if ($updatedRelatedCoursesIDs === $programme->$related_courses_field) {
			echo "\tCourse already related by Instance ID - no update required\n\n";
			return; 
		}

		echo "\tUpdating with: $updatedRelatedCoursesIDs\n\n";

		DB::table($programmes_table)
			->where('id', '=', $programme->id)
			->update(array($related_courses_field => $updatedRelatedCoursesIDs));
	}

	/**
	 * view or update the related programmes
	 *
	 * @param string $mode (either 'view' or 'update')
	 * @return void
	 */
	private function progressProgrammes($mode = view)
	{
		Auth::login(1);
	
		$levels = array('ug', 'pg');
	
		foreach($levels as $level) {
			$programmesTable = "programmes_$level";
			$programmesModel = strtoupper($level) . '_Programme';
			$related_courses_field = $programmesModel::get_related_courses_field();
			$programme_title_field = $programmesModel::get_programme_title_field();
		
			foreach(DB::table($programmesTable)->get() as $programme) {
				$relatedCoursesIDs = trim($programme->$related_courses_field, ',');
	
				if (strlen($relatedCoursesIDs) !== 0) {
					$relatedCoursesIDsArray =  explode(',', $relatedCoursesIDs);
					echo "\n{$programme->$programme_title_field} is related to programmes: $relatedCoursesIDs \n";
					
					$relatedCourseInstanceIDsArray = array();

					foreach($relatedCoursesIDsArray as $relatedCourseID) {
						$relatedCourse = static::getProgramme($relatedCourseID, $level);
						$relatedCourseInstanceIDsArray[] = $relatedCourse->instance_id;
						printf(
							"\t(ID: %4d / instanceID: %4d) - %s\n",
							$relatedCourseID,
							$relatedCourse->instance_id,
							$relatedCourse->$programme_title_field
						);
					}
					
					if ('update' === $mode) {
						self::updateRelatedProgrammes($programme, $level, $relatedCourseInstanceIDsArray);
					}
				}
			}
		}	
	}

	public function view()
	{
		self::progressProgrammes('view');
	}


	public function update()
	{
		self::progressProgrammes('update');
	}
}
	