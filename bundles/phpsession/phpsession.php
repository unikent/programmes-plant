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
		/*
		if (file_exists($path = $this->path.$id))
		{
			return unserialize(file_get_contents($path));
		}
		
		
		if (\Laravel\Cookie::has(Cookie::payload))
		{
			return unserialize(Crypter::decrypt(\Laravel\Cookie::get(Cookie::payload)));
		}
		*/
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
		
		/*
		public function save($session, $config, $exists)
		{
			file_put_contents($this->path.$session['id'], serialize($session), LOCK_EX);
		}
	
		extract($config, EXTR_SKIP);

		$payload = Crypter::encrypt(serialize($session));

		\Laravel\Cookie::put(Cookie::payload, $payload, $lifetime, $path, $domain);
		*/
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
	
		//\Laravel\Cookie::forget(Cookie::payload);
	}

}