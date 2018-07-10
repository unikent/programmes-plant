<?php

/*
	2018-07-10 
	this is a one off task to update the IDs used for the related courses 

	Situation was that the IDs which has been saved for the related courses where the course ids rather than their instance ids
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

	public function run($arguments = array())
	{
		print_r($arguments);
	}

	
	private function getProgramme($id, $level = 'ug')
	{
		$programmes_table = "programmes_$level";
		$programmes_model = strtoupper($level) . '_Programme';
		$programme_title_field = $programmes_model::get_programme_title_field();
		$programme = DB::table($programmes_table)->where('id', '=', $id)->first();

		return $programme;
	}


	public function view($arguments = array())
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
					foreach($relatedCoursesIDsArray as $relatedCourseID) {
						$relatedCourse = static::getProgramme($relatedCourseID, $level);
						echo "\tID: $relatedCourseID - InstanceID: {$relatedCourse->instance_id}\t{$relatedCourse->$programme_title_field}\n";
					}
				}
			}
		}	
	}


	public function update()
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

					$updatedRelatedCoursesIDsArray = array();
					
					foreach($relatedCoursesIDsArray as $relatedCourseID) {
						$relatedCourse = static::getProgramme($relatedCourseID, $level);
						echo "\tID: $relatedCourseID - InstanceID: {$relatedCourse->instance_id}\t{$relatedCourse->$programme_title_field}\n";
						$updatedRelatedCoursesIDsArray[] = $relatedCourse->instance_id;
					}

					// the original data began with a ',' so I'm keeping that here 
					$updatedRelatedCoursesIDs = ',' . implode(',', $updatedRelatedCoursesIDsArray);
					echo "Updating with: $updatedRelatedCoursesIDs\n\n";
					$programme->$related_courses_field = implode(',', $updatedRelatedCoursesIDsArray);
					
					// @TODO this isn't working yet
					$programme->save();
				}
			}
		}	
	}
}
	