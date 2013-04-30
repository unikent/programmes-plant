<?php

return array(
	/**
	 * emails to users
	 */
	'admin_notification' => array('title' => "New programme-factory update for :title",
									'body' => ":author has submitted a new programme-factory update for :title, which is currently <a href='https://webtools.kent.ac.uk/programmes/editor/inbox'>pending approval</a>."
							),
	
	/**
	 * emails from users
	 */
	'user_notification' => array(
								'approve' => array('title' => "Programme-plant updates approved",
													'body' => "Dear :author,<br><br>
												Regarding your recent updates to <a href='https://webtools.kent.ac.uk/programmes/2014/ug/programmes/edit/:id'>:title</a> on the programmes-plant: these have now been approved and will shortly appear on the live website at <a href='http://www.kent.ac.uk/courses/undergraduate/:id/:slug'>http://www.kent.ac.uk/courses/undergraduate/:id/:slug</a><br><br>
												Feel free to reply to this email if you have any questions about your updates.<br><br>
												Regards,<br><br>
												EMS Publishing Office<br><br>"
											),
								'request' => array('title' => "RE: Your updates to :title"),
							),
		
);