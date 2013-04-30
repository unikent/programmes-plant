<?php

return array(
	// statuses
	'status_current' 				=> 'Current revision',
	'status_draft' 					=> 'R',
	'status_unused' 				=> 'Unused',
	'status_live'					=> 'Published',
	'status_live_published_text'	=> "Published on :date by :user",
	'status_review'					=> 'Under Review',
	'status_draft_rollback' 		=> 'R',
	'status_previous_live_rollback' => 'R',

	// Headings
	'active_revisions' 		=> 'Active revisions',
	'rollback_revisions'	=> 'Roll live revision back',
	'rollback_warning'		=> 'Warning. Using this feature will change the revision published on the live website.',
	'historical_revisions'	=> 'Previous revisions',

	// Buttons
	'manage_revisions' 	=> 'Manage revisions',
	'make_live' 		=> 'Make live',
	'use_previous' 		=> 'Use previous',
	'use_revision' 		=> 'Use revision',
	'diff_live' 		=> 'Differences from live',
	'rollback_live' 	=> 'Roll live back to revision',
	'cancel' 			=> 'Not right now',
	'unpublish'         => 'Unpublish',
	'view_preview'		=> 'Preview',
	'send_for_editing'	=> 'Send to EMS',

	//Links
	'edit_form'	 		=> 'Return to edit form',
	'rollback_form' 	=> 'Emergency rollback options',
	'revision_form' 	=> 'View active revisions',

	//Info
	'locked_warning'	 			=> 'Warning: This programme contains changes that are not yet ready to go live.',
	'locked_draft_tag'	 			=> 'Draft',
	'locked_under_review'	 		=> 'Changes have been sent to EMS for publishing.',
	'locked_not_under_review'		=> 'The latest changes to this programme have not yet been sent to EMS for publishing.',
	
	'modal_make_live_header'	 	=> 'Are You Sure?',
	'modal_make_live_body'	 		=> '<p>This will make the currenty selected revision live, meaning it will be visable on the course pages.</p><p>Are you sure you want to do this?</p>',
	
	'modal_send_for_editing_header'				=> 'Are You Sure?',
	'modal_send_for_editing_under_review_body'	=> '<p><strong>You have already made edits to this and sent them for review by EMS</strong></p><p>Are you sure you want to send it again?</p>',
	'modal_send_for_editing_body'	 			=> '<p>This means that you have completed edits and that you are sending this version of programme to EMS for proofing and editing.</p><p>Are you sure you want to do this?</p>',

	
);
