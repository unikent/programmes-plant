<?php 

class PhpSession extends \Laravel\Session\Drivers\Driver {
	private $sess_name = 'xcri';
		
	public function __construct()
	{
		if (!isset($_SESSION)) {
			session_start();
		}
	}

	/**
	 * Load a session from storage by a given ID.
	 *
	 * If no session is found for the ID, null will be returned.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		if(isset($_SESSION[$this->sess_name.$id])){
			return unserialize($_SESSION[$this->sess_name.$id]);
		}
	}

	/**
	 * Save a given session to storage.
	 *
	 * @param  array  $session
	 * @param  array  $config
	 * @param  bool   $exists
	 * @return void
	 */
	public function save($session, $config, $exists)
	{
		$_SESSION[$this->sess_name.$session['id']] = serialize($session);
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		unset($_SESSION[$this->sess_name.$session['id']]);
	}

}