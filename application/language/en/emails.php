<?php

return array(
	/**
	 * emails to admin
	 */
	'admin_notification' => array('title' => "New programme-factory update for :title :awards",
									'body' => "Dear EMS Publishing Office,<br /><br />:author has submitted a new programme-plant update for :title :awards, which is currently :link_to_inbox.",
									'pending_approval_text' => "pending approval"
							),
	
	/**
	 * emails to users
	 */
	'user_notification' => array(
								'approve' => array('title' => "programme-plant updates approved for :title :awards",
													'body' => "Dear :author,<br /><br />
												Regarding your recent updates to :link_to_edit_programme on the programmes-plant: these have now been approved and will shortly appear on the live website at :link_to_programme_frontend <br /><br />
												Feel free to reply to this email if you have any questions about your updates.<br /><br />
												Regards,<br /><br />
												EMS Publishing Office<br /><br />"
											),
								'request' => array('title' => "RE: Your updates to :title :awards",
													'body' => "Your changes to <a href=':link'>:title :awards</a> have been reviewed but not yet published. Please see the following comments:<br /><br />"
													),
							),
	'new_user_notification' => array(
									'title' => "Programme-plant access",
									'body' => "Dear :user,<br /><br />You have just been given access to the programme-plant (<a href='https://webtools.kent.ac.uk/programmes'>https://webtools.kent.ac.uk/programmes</a>).<br /><br />When you click the link above you will be asked to enter your University of Kent IT account username and password."
							)
		
);
