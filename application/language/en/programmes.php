<?php
return array(
	'ug' => 'undergraduate',
	'pg' => 'postgraduate',
	'ug_introduction' => 'This is the listing for <strong>undergraduate programmes</strong> beginning in <strong>:year</strong>.',
	'pg_introduction' => 'This is the listing for <strong>postgraduate programmes</strong> beginning in <strong>:year</strong>.',
	'create_introduction' => 'This form lets you enter information for a new programme.',
	'edit_introduction' => 'This form lets you edit information for an existing programme.',
	'edit_programme' => 'Edit',
	'clone' => 'Clone',
	'delete' => 'Delete programme',
	'delete_title' => 'Deleting a programme',
	'delete_message' => 'Deleting a programme will remove it from the course pages. You will be asked for further confirmation if you do press the delete button.',
	'delete_modal_title' => 'Deleting a programme',
	'delete_modal_message' => 'Are you sure you want to detele this programme and remove it from the University course pages.',
	'delete_modal_cancel' => 'Cancel',
	'delete_modal_delete' => 'Delete programme',
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


	'diff_edit_programme' => 'Edit this programme',
	'diff_request_amends' => 'Request changes from author',
	'diff_approve_revision' => 'Approve revision',
	'diff_reject_revision' => 'Reject revision',

	'diff_header' => 'Accept changes',
	'diff_intro' => 'The following shows the differences between the two revisions.',
	'diff_table_live_header' => 'Live, :date',
	'diff_table_proposed_header' => 'Proposed, :date',

	'diff_modal' => array(
		'request_changes' => array(
			'header' => 'Request changes from author',
			'body' => '<p>Use the form below to send a brief message to the author of this revision.</p>',
			'submit' => 'Send message',
		),
		'approve_revision' => array(
			'header' => 'Are you sure?',
			'body' => '<p>This will make the currenty selected revision live, meaning it will be visible on the course pages.</p><p>Are you sure?</p>',
			'submit' => 'Approve',
		),
		'reject_revision' => array(
			'header' => 'Are you sure?',
			'body' => '<p>This will reject the revision. Are you sure?</p>',
			'submit' => 'Reject',
		),
		'cancel' => 'Cancel',
	),
);