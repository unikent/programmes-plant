<?php

return array(
	/**
	 * Buttons
	 */
	'actions_column' => 'Actions',
	'edit' => 'Edit',
	'delete' => 'Remove',
	'create' => 'Make a new campus',
	'back' => 'Cancel',
	'save' => 'Save',

	/**
	 * Index page.
	 */
	'introduction' => 'This is a list of all the University of Kent campuses.',
	'no_items' => 'No campuses yet',

	// Modal On Deletion Attempt
	'modal_header' => 'Are you sure?',
	'modal_body' => 'This will remove this campus, are you sure you want to do this?',
	'modal_keep' => 'No - Keep This Campus',
	'modal_delete' => 'Remove This Campus',

	/**
	 * Forms
	 */
	'edit_introduction' => 'Edit this campus below.',
	'create_introduction' => 'Create the new campus below.',

	'form' => array(
		'edit' => array(
			'header' => 'Edit Campus',
		),
		'new' => array(
			'header' => 'New Campus',
		),
		'details_header' => 'Campus Details',
		'name' => array(
			'label' => 'Name',
			'placeholder' => 'Enter campus name...',
		),
		'title' => array(
			'label' => 'Title',
			'placeholder' => 'Campus title',
		),
		'address_1' => array(
			'label' => 'Address Line 1',
			'placeholder' => 'Campus address line 1',
		),
		'address_2' => array(
			'label' => 'Address Line 2',
			'placeholder' => 'Campus address line 2',
		),
		'address_3' => array(
			'label' => 'Address Line 3',
			'placeholder' => 'Campus address line 3',
		),
		'town' => array(
			'label' => 'Town',
			'placeholder' => 'Campus town',
		),
		'postcode' => array(
			'label' => 'Postcode',
			'placeholder' => 'Campus postcode',
		),
		'email' => array(
			'label' => 'Email',
			'placeholder' => 'Campus email',
		),
		'phone' => array(
			'label' => 'Telephone',
			'placeholder' => 'Campus telephone',
		),
		'url' => array(
			'label' => 'Website URL',
			'placeholder' => 'Campus website URL',
		),
		'description' => array(
			'label' => 'Description',
			'placeholder' => 'Campus description',
		),
		'identifier' => array(
			'label' => 'Identifier',
			'placeholder' => 'Campus identifier',
		),
	),

	/**
	 * Messages
	 */
	'error' => array(
		'delete' => 'Campus not found',
	),

	'success' => array(
		'delete' => 'Campus successfully removed.',
		'create' => 'Campus successfully created',
		'edit' => 'Campus saved',
	)
);
