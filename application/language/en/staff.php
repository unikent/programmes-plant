<?php

return array(
	/**
	 * Buttons
	 */
	'actions_column' => 'Actions',
	'edit' => 'Edit',
	'delete' => 'Remove',
	'create' => 'Add new staff member',
	'back' => 'Cancel',
	'save' => 'Save',

	/**
	 * Index page.
	 */
	'introduction' => 'Reasearch staff for PG programmes',
	'no_items' => 'No staff yet.',

	// Modal On Deletion Attempt
	'modal_header' => 'Are you sure?',
	'modal_body' => 'This will remove this staff member, are you sure you want to do this?',
	'modal_keep' => 'No - Keep This staff member',
	'modal_delete' => 'Remove This staff member',

	/**
	 * Forms
	 */
	'edit_introduction' => 'Edit staff member.',
	'create_introduction' => 'Add staff member',

	'form' => array(
		'login' => array(
			'label' => 'Login',
			'placeholder' => 'Staff login name',
		),
		'title' => array(
			'label' => 'Title',
			'placeholder' => 'Mr/Ms/Dr/Professor',
		),
		'forename' => array(
			'label' => 'First name',
			'placeholder' => 'Joe',
		),
		'surname' => array(
			'label' => 'Surname',
			'placeholder' => 'Smith',
		),
		'email' => array(
			'label' => 'Email',
			'placeholder' => 'person@kent.ac.uk',
		),
		'profile_url' => array(
			'label' => 'Profile Url',
			'placeholder' => 'Url to staff members profile page',
		),
		'blurb' => array(
			'label' => 'Blurb'
		),
		'subject' => array(
			'label' => 'Subject'
		),
	),

	/**
	 * Messages
	 */
	'error' => array(
		'delete' => 'Staff member not found',
	),

	'success' => array(
		'delete' => 'Staff member successfully removed.',
		'create' => 'Staff member successfully created',
		'edit' => 'Staff member saved',
	)
);
