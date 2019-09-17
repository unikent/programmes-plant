<?php

return array(
	/**
	 * Buttons
	 */
	'actions_column' => 'Actions',
	'edit' => 'Edit',
	'delete' => 'Remove',
	'create' => 'Add new Student Profile',
	'export' => 'Export Student Profiles as CSV',
	'back' => 'Cancel',
	'save' => 'Save',

	/**
	 * Index page.
	 */
	'title' => 'Student Profiles',
	'introduction' => '',
	'no_items' => 'No profiles yet.',

	// Modal On Deletion Attempt
	'modal_header' => 'Are you sure?',
	'modal_body' => 'This will remove this Student Profile, are you sure you want to do this?',
	'modal_keep' => 'No - Keep this Student Profile',
	'modal_delete' => 'Remove this Student Profile',

	/**
	 * Forms
	 */
	'edit_introduction' => 'Edit Student Profile.',
	'create_introduction' => 'Add Student Profile',

	'form' => array(
		'name' => array(
			'label' => 'Student Name'
		),
		'type' => array(
			'label' => 'Student Type'
		),
		'slug' => array(
			'label' => 'Url slug'
		),
		'interview_month' => array(
			'label' => 'Interview Month',
		),
		'interview_year' => array(
			'label' => 'Interview Year',
		),
		'course' => array(
			'label' => 'Course'
		),
		'banner_image_id' => array(
			'label' => 'Banner Image'
		),
		'profile_image_id'=>array(
			'label' => 'Profile Image'
		),
		'subject_categories' => array(
			'label' => 'Subject Categories'
		),
		'quote' => array(
			'label' => 'Quote'
		),
		'video' => array(
			'label' => 'Video'
		),
		'lead' => array(
			'label' => 'Lead'
		),
		'content' => array(
			'label' => 'Content'
		),
		'links' => array(
			'label' => 'Additional Links'
		),
	),

	/**
	 * Messages
	 */
	'error' => array(
		'delete' => 'Profile not found',
	),

	'success' => array(
		'delete' => 'Student Profile successfully removed.',
		'create' => 'Student Profile successfully created',
		'edit' => 'Student Profile saved',
	)
);
