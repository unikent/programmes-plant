<?php

return array(
	/**
	 * emails to admin
	 */
	'admin_notification' => array('title' => "New programme-factory update for :title",
									'body' => "Dear EMS Publishing Office,<br /><br />:author has submitted a new programme-plant update for :title, which is currently :pending_approval."
							),
	
	/**
	 * emails to users
	 */
	'user_notification' => array(
								'approve' => array('title' => "programme-plant updates approved",
													'body' => "Dear :author,<br /><br />
												Regarding your recent updates to <a href='https://webtools.kent.ac.uk/programmes/2014/ug/programmes/edit/:id'>:title</a> on the programmes-plant: these have now been approved and will shortly appear on the live website at <a href='http://www.kent.ac.uk/courses/undergraduate/:id/:slug'>http://www.kent.ac.uk/courses/undergraduate/:id/:slug</a><br /><br />
												Feel free to reply to this email if you have any questions about your updates.<br /><br />
												Regards,<br /><br />
												EMS Publishing Office<br /><br />"
											),
								'request' => array('title' => "RE: Your updates to :title"),
							),
		
);
