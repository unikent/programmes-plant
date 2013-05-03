<?php

return array(
	/**
	 * Buttons
	 */
	'actions_column' => 'Actions',
	'edit' => 'Edit',
	'delete' => 'Remove',
	'create' => 'Make a new award',
	'back' => 'Cancel',
	'save' => 'Save',

	/**
	 * Index page.
	 */
	'introduction' => 'This lists all awards that can be applied to programmes.',
	'no_items' => 'No awards yet',

	// Modal On Deletion Attempt
	'modal_header' => 'Are you sure?',
	'modal_body' => 'This will remove this award, are you sure you want to do this?',
	'modal_keep' => 'No - Keep This Award',
	'modal_delete' => 'Remove This Award',

	/**
	 * Forms
	 */
	'edit_introduction' => 'Edit this award below.',
	'create_introduction' => 'Create the new award below.',

	'form' => array(
		'edit' => array(
			'header' => 'Edit Award',
		),
		'new' => array(
			'header' => 'New Award',
		),
		'details_header' => 'Award Details',
		'name' => array(
			'label' => 'Name',
			'placeholder' => 'Enter award name...',
		),
		'long_name' => array(
			'label' => 'Long name',
			'placeholder' => 'Enter long award name...',
		),
	),

	/**
	 * Messages
	 */
	'error' => array(
		'delete' => 'Award not found',
	),

	'success' => array(
		'delete' => 'Award successfully removed.',
		'create' => 'Award successfully created',
		'edit' => 'Award saved',
	)
);
