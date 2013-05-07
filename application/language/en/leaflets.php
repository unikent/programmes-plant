<?php

return array(
	/**
	 * Buttons
	 */
	'actions_column' => 'Actions',
	'edit' => 'Edit',
	'delete' => 'Remove',
	'create' => 'Make a new leaflet',
	'back' => 'Cancel',
	'save' => 'Save',

	/**
	 * Index page.
	 */
	'introduction' => 'This lists all programme leaflets.',
	'no_items' => 'No leaflets yet',

	// Modal On Deletion Attempt
	'modal_header' => 'Are you sure?',
	'modal_body' => 'This will remove this award, are you sure you want to do this?',
	'modal_keep' => 'No - Keep This Leaflet',
	'modal_delete' => 'Remove This Leaflet',

	/**
	 * Forms
	 */

	'form' => array(
		'edit' => array(
			'header' => 'Edit Leaflet',
		),
		'new' => array(
			'header' => 'New Leaflet',
		),
		'details_header' => 'Leaflet Details',
		'name' => array(
			'label' => 'Name',
			'placeholder' => 'Enter Leaflet name...',
		),
		'campus' => array(
			'label' => 'Campus',
		),
		'tracking_code' => array(
			'label' => 'Tracking code',
			'placeholder' => 'Enter Tracking code...',
		),
	),

	/**
	 * Messages
	 */
	'error' => array(
		'delete' => 'Leaflet not found',
	),

	'success' => array(
		'delete' => 'Leaflet successfully removed.',
		'create' => 'Leaflet successfully created',
		'edit' => 'Leaflet saved',
	)
);
