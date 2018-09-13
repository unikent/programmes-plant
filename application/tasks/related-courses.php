<?php

/*
	2018-07-10 
	this is a one off task to update the IDs used for the related courses 

	Situation was that the IDs which has been saved for the related courses were the course ids rather than their instance ids
	done as part of https://github.com/unikent/programmes-plant/pull/959
	
	usage:
	
	// to see the current state of all the courses and their related courses
	php artisan related-courses:view year=<year> level=<level>
	
	// to update the related courses so that they are related by instance id rather than id
	php artisan related-courses:update year=<year> level=<level>
	
	See Christian for more details if needed

*/
class Related_Courses_Task {
	
	/**
	 * getProgramme - gets a programme
	 *
	 * @param int 	$id	
	 * @param string level - 'ug' or 'pg'
	 * @return stdClass - the programme model
	 */
	private function getProgramme($id, $level = 'ug')
	{
		$level = strtolower($level);
		$programmes_model = strtoupper($level) . '_Programme';
		$programme = $programmes_model::find($id);

		return $programme;
	}

	/**
	 * replaces the related courses for a given programme at a given level 
	 *
	 * @param mixed 	$programme - the programme model to update
	 * @param string 	level - 'ug' or 'pg'
	 * @param array 	$updatedRelatedCoursesIDsArray - instance IDs for courses to relate tp $programme
	 * @return void
	 */
	private function updateRelatedProgrammes($programme, $level, $updatedRelatedCoursesIDsArray)
	{

		if (0 === count($updatedRelatedCoursesIDsArray)) {
			return;
		}

		// Do not do this if we have a revision in-progress for this programme
		// instead log it somehow 
		if ($programme->current_revision !== $programme->live_revision) {
			echo "\tCourse has draft version in progress -- not updating\n\n";
			return; 
		}

		// the original data began with a ',' so I'm keeping that here 
		$updatedRelatedCoursesIDs = ',' . implode(',', $updatedRelatedCoursesIDsArray);

		// figure out database cols
		$programmes_model = strtoupper($level) . '_Programme';
		$related_courses_field = $programmes_model::get_related_courses_field();
		
		if ($updatedRelatedCoursesIDs === $programme->$related_courses_field) {
			echo "\tCourse already related by Instance ID - no update required\n\n";
			return; 
		}

		echo "\tUpdating with: $updatedRelatedCoursesIDs\n\n";

		$programme->$related_courses_field = $updatedRelatedCoursesIDs;
		$programme->save();

		// TODO: make the current revision live
		$programme->make_revision_live($programme->current_revision);
	}

	/**
	 * view or update the related programmes
	 *
	 * @param string $mode (either 'view' or 'update')
	 * @return void
	 */
	private function progressProgrammes($mode = view, $arguments)
	{
		Auth::login(1); // logs in as 'rollover'
	
		$level = $arguments['level'];
		$year = $arguments['year'];
	
		$programmesTable = strtolower("programmes_$level");
		$programmesModel = "{$level}_Programme";
		
		$related_courses_field = $programmesModel::get_related_courses_field();
		$programme_title_field = $programmesModel::get_programme_title_field();

		foreach($programmesModel::where('year', '=', $year)->get() as $programme) {
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

	public function view($arguments = array())
	{
		self::progressProgrammes('view', self::parseArguments($arguments));
	}


	public function update($arguments = array())
	{
		self::progressProgrammes('update', self::parseArguments($arguments));
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

		if(!isset($parsedArguments['year'], $parsedArguments['level'])) {
			self::printUsage();
		}

		$parsedArguments['level'] = strtoupper($parsedArguments['level']);

		if (!is_numeric($parsedArguments['year']) || strlen($parsedArguments['year']) !== 4) {
			self::printUsage();
		}

		if ($parsedArguments['level'] !== 'UG' && $parsedArguments['level'] !== 'PG') {
			self::printUsage();
		}

		return $parsedArguments;
	}

	private function printUsage()
	{
		echo 'usage:

// to see the current state of all the courses and their related courses
php artisan related-courses:view year=<year> level=<level>

// to update the related courses so that they are related by instance id rather than id
php artisan related-courses:update year=<year> level=<level>
';

		die();
	}
}
	