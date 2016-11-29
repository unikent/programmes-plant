<?php

return array(
	/**
	 * Buttons
	 */
	'actions_column' => 'Actions',
	'edit' => 'Edit',
	'delete' => 'Remove',
	'create' => 'New image',
	'back' => 'Cancel',
	'save' => 'Save',

	/**
	 * Index page.
	 */
	'introduction' => 'This is a list of all images in the Programmes Plant.',
	'no_items' => 'No Images yet',

	// Modal On Deletion Attempt
	'modal_header' => 'Are you sure?',
	'modal_body' => 'This will remove delete the image, are you sure you want to do this?',
	'modal_keep' => 'No - Keep This Images',
	'modal_delete' => 'Remove this Image',

	/**
	 * Forms
	 */
	'edit_introduction' => 'Edit this campus below.',
	'create_introduction' => 'Create the new campus below.',

	'form' => array(
		'edit' => array(
			'header' => 'Edit image',
		),
		'new' => array(
			'header' => 'New image',
		),
		'details_header' => 'Image details',
		'name' => array(
			'label' => 'Name',
			'placeholder' => 'Enter image name...',
		),
		'caption' => array(
			'label' => 'Caption',
			'placeholder' => 'Image caption',
		),
		'attribution_text' => array(
			'label' => 'Attribution text',
			'placeholder' => 'Image attribrution text',
		),
		'attribution_link' => array(
			'label' => 'Attribution link',
			'placeholder' => 'Image attribrution link',
		),
		'licence_link' => array(
			'label' => 'Licence Link',
			'placeholder' => 'Like to image licence',
		),
		'focus' => array(
			'label' => 'Default image focus position',
		),
		'image' => array(
			'label' => 'Upload new image',
		),
	),

	/**
	 * Messages
	 */
	'error' => array(
		'delete' => 'Image not found',
	),

	'success' => array(
		'delete' => 'Image successfully deleted.',
		'create' => 'Image successfully deleted',
		'edit' => 'Image saved',
	)
);
