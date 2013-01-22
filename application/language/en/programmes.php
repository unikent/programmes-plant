<?php
return array(
	'ug' => 'undergraduate',
	'pg' => 'gostgraduate',
	'ug_introduction' => 'This is the listing for <strong>undergraduate programmes</strong> beginning in <strong>:year</strong>.',
	'pg_introduction' => 'This is the listing for <strong>postgraduate programmes</strong> beginning in <strong>:year</strong>.',
	'create_introduction' => 'This form lets you enter information for a new programme.',
	'edit_introduction' => 'This form lets you edit information for an existing programme.',
	'edit_programme' => 'Edit',
	'clone' => 'Clone',
	'delete_programme' => 'Delete',
	'table_title' => 'Title',
	'table_subject' => 'Subject',
	'table_excerpt' => 'Content excerpt',
	'actions' => 'Actions',

	'save' => 'Save',
	'create_programme' => 'Make a new programme',
	'back' => 'Cancel',
	
	'create_programme_title' => 'New programme',

	// Fields
	'year' => 'Year of programme',
	'title' => 'Programme title',
	'slug' => 'Slug',
	'slug_help' => 'The desired short URL, for example <code>/business-studies</code>',
	'summary' => 'Summary of programme',
	'summary_help' => 'A brief, but interesting summary of the programme.',
	'school_id' => 'School ID',
	'school_adm_id' => 'School Admin ID',
	'campus_id' => 'Campus ID',
	'programme_id' => 'Programme ID',
	'honours' => 'Honours type ID',

	'related_school_ids' => 'Related Schools',
	'related_subject_ids' => 'Related subjects',
	'related_programme_ids' => 'Related Programmes',
	'leaflet_ids' => 'Leaflets',

	'content' => 'Programme information',
	'additional_leaflet_urls_help' => 'Please enter a url and press enter to add it to the list of urls.',

	// Messages
	'no_programmes' => 'There are no programmes created yet for the :level programme in :year.',
	'no_change_requests' => 'There are currently no current change requests in the queue.',

	//field text
	'withdrawn_field_text' => 'Withdrawn',
	'suspended_field_text' => 'Suspended',
	'subject_to_approval_field_text' => 'Subject to approval',
	
	'traffic-lights' => array(
		'published' => array(
			'label' => 'Published',
			'tooltip' => '\'Published\' marks programmes where the most recently edited version is also the live version.',
		),
		'editing' => array(
			'label' => 'Editing',
			'tooltip' => '\'Editing\' marks programmes which have been edited since they were last pushed to live.',
		),
		'new' => array(
			'label' => 'New',
			'tooltip' => '\'New\' marks programmes which have never been pushed to live.',
		),
	),
);