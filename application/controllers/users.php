<?php

class Users_Controller extends Admin_Controller {

	public $restful = true;

	// limit view to manage users only
	public $required_permissions = array("manage_users");

	/**
	 * List users currently in system
	 *
	 * @route /users
	 */
	public function get_index()
	{
		$this->layout->nest('content', 'admin.users.index',  array('users' => User::get()));
	}

	/**
	 * Edit a user page
	 *
	 * @route /users/edit/{id}
	 */
	public function get_edit($id = false)
	{	

		// If no id
		if(!$id) return Redirect::to('users');

		// Get user
		$current_user = User::find($id);

		// get role is listable format
		if(Auth::user()->is('Hyper Administrator')){
			$roles = Role::all();
		} else {
			$roles = Role::all(true);
		}

		foreach($roles as $role) $role_list[$role->id] = $role->name;
		// SHow edit form
		$this->layout->nest('content', 'admin.users.form', array("create"=>false, 'user'=>$current_user, "roles" =>  $role_list));
	}

	/**
	 * Add user page
	 *
	 * @route /users/add
	 */
	public function get_add()
	{	
		// get role is listable format
		if(Auth::user()->is('Hyper Administrator')){
			$roles = Role::all();
		} else {
			$roles = Role::all(true);
		}

		foreach($roles as $role) $role_list[$role->id] = $role->name;
		// Show add form
		$this->layout->nest('content', 'admin.users.form', array("create"=> true, "roles" =>  $role_list));
	}

	/**
	 * Add a user
	 *
	 * @route /users/add
	 */
	public function post_add(){
		// grab standard details
		$username = Input::get('username');
		$role = Input::get('role');
		$ug_subjects = Input::get('ug_subjects');
		$pg_subjects = Input::get('pg_subjects');
		if(!Auth::user()->is('Hyper Administrator')){
			if(in_array($role, Role::get_protected())){
				Messages::add('error', 'Unauthorised assignment of roles.');
				return Redirect::to('users');
			}
		}

		$this->updateUser($username, $role, $ug_subjects, $pg_subjects);

		// Send email notification to the user
		if(Config::get('programme_revisions.notifications.on')){
			$user = User::where('username', '=', $username)->first();
			$user_email = !empty($user) ? $user->email : '';

			$mailer = IoC::resolve('mailer');
			$message = Swift_Message::newInstance(__('emails.new_user_notification.title'))
				->setFrom(Config::get('programme_revisions.notifications.from'))
				->setTo($user_email)
				->addPart(__('emails.new_user_notification.body', array('user'=>$user->fullname)), 'text/html');
			$mailer->send($message);
		}

		return Redirect::to('users');			
	}
	/**
	 * edit a user
	 *
	 * @route /users/edit/{id}
	 */
	public function post_edit(){
		// grab standard details
		$username = Input::get('username');
		$role = Input::get('role');
		$ug_subjects = Input::get('ug_subjects');
		$pg_subjects = Input::get('pg_subjects');

		if(!Auth::user()->is('Hyper Administrator')){
			if(in_array($role, Role::get_protected())){
				Messages::add('error', 'Unauthorised assignment of roles.');
				return Redirect::to('users');
			}
		}

		$this->updateUser($username, $role, $ug_subjects, $pg_subjects);
		return Redirect::to('users');
	}

	/**
	 * delete a user
	 *
	 * @route /users/delete/{id}
	 */
	public function get_delete($user_id = false)
	{
		$user = User::find($user_id);
		if($user !== null){
			// Clear roles so deletion is possible
			$user->roles()->sync(array());
			// delete em
			$user->delete();
		}

		return Redirect::to('users');
	}


	/**
	 * Update user: will either create new user or update an existing one
	 *
	 * @param $username of user
	 * @param $role of user
	 * @param $subjects array of subjects user can manage
	 */
	protected function updateUser($username, $role, $ug_subjects,$pg_subjects){

		// convert subjects to , seperated list (remove any blanks)
		$ug_subjects = is_array($ug_subjects) ? implode(',', array_filter($ug_subjects)) : '';
		$pg_subjects = is_array($pg_subjects) ? implode(',', array_filter($pg_subjects)) : '';

		// Attempt to load user from ldap
		$ldap = LDAPConnect::instance();
		
		// Re: the str_replace
		// Allow authentication using test users (Normal username -test). Test accounts will authenticate normally
		// using the parent usernames credentals, but can be setup in the admin backend to have custom permissions.

		$userdata = $ldap->getUserAnonymous(str_replace('-test','',$username));

		if($userdata != false){
			// grab ldap details
			$fullname = $userdata[0]['displayname'][0];
			$email = $userdata[0]['mail'][0];
			$department = $userdata[0]['unikentoddepartment'][0];

			// Attempt to get existing user
			$user = User::where('username', '=', $username)->first();

			// if user doesnt exist, create em
			if($user === null) $user = new User();
			
			$user->username = $username;
			$user->fullname = $fullname;
			$user->ug_subjects = $ug_subjects;
			$user->pg_subjects = $pg_subjects;
			$user->email = $email;
			$user->department = $department;
			$user->verified = 1;
			$user->save();
			// set role
			$user->roles()->sync(array($role));
		}else{

			// bad username, doesnt exist in ldap!
		}
	}
}