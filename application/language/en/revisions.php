<?php

return array(
	// statuses
	'status_current' 	=> 'Current revision',
	'status_draft' 		=> 'R',
	'status_unused' 	=> 'Unused',
	'status_live'		=> 'Published',
	'status_review'		=> 'Under Review',
	'status_draft_rollback' => 'R',
	'status_previous_live_rollback' => 'R',

	// Headings
	'active_revisions'	=> 'Active revisions',
	'rollback_revisions' => 'Roll live revision back',
	'rollback_warning' 	=> 'Warning. Using this feature will change the revision published on the live website.',
	'historical_revisions' => 'Previous revisions',
	'draft'				=> 'Draft',
	'draft_warning'		=> 'Warning: This programme contains changes that are not yet ready to go live.',
	'under_review_warning' => 'Changes have been sent to EMS for publishing.',
	'review_warning'	=> 'The latest changes to this programme have not yet been sent to EMS for publishing.',

	// Buttons
	'manage_revisions' 	=> 'Manage revisions',
	'review'			=> 'Review',
	'make_live' 		=> 'Make live',
	'use_previous' 		=> 'Use previous',
	'use_revision' 		=> 'Use revision',
	'diff_live' 		=> 'Differences from live',
	'rollback_live' 	=> 'Roll live back to revision',
	'cancel' 			=> 'Not right now',
	'unpublish'         => 'Unpublish',
	'view_preview'		=> 'Preview',
	'view_simpleview'	=> 'Data for print view',
	'send_for_editing'	=> 'Send to EMS',

	//Links
	'edit_form'	 		=> 'Return to edit form',
	'rollback_form' 	=> 'Emergency rollback options',
	'revision_form' 	=> 'View active revisions',
	
	// modal warnings etc
	'modals' => array(
					'live_warning' => 'This will make the currently selected revision live, meaning it will be visible on the live course page.',
					'under_review_warning' => '<p><strong>You have already made edits to this and sent them for review by EMS</strong></p>
	<p>Are you sure you want to send it again?</p>',
					'review_warning' => 'You have completed your edits and you are about to send this version of the programme to EMS for proofing and editing.',
					
	),
	
);
