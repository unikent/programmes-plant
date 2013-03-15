<?php
// use user and role objects from namespace
use \Verify\Models\user;

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
		$subjects = Input::get('subjects');

		if(!Auth::user()->is('Hyper Administrator')){
			if(in_array($role, Role::get_protected())){
				Messages::add('error', 'Unauthorised assignment of roles.');
				return Redirect::to('users');
			}
		}

		$this->updateUser($username, $role, $subjects);
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
		$subjects = Input::get('subjects');

		if(!Auth::user()->is('Hyper Administrator')){
			if(in_array($role, Role::get_protected())){
				Messages::add('error', 'Unauthorised assignment of roles.');
				return Redirect::to('users');
			}
		}

		$this->updateUser($username, $role, $subjects);
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
	protected function updateUser($username, $role, $subjects){

		// convert subjects to , seperated list (remove any blanks)
		$subjects = is_array($subjects) ? implode(',', array_filter($subjects)) : '';

		// Attempt to load user from ldap
		$ldap = LDAPConnect::instance();
		$userdata = $ldap->getUserAnonymous($username);

		if($userdata != false){
			// grab ldap details
			$fullname = $userdata[0]['displayname'][0];
			$email = $userdata[0]['mail'][0];

			// Attempt to get existing user
			$user = User::where('username','=',$username)->first();

			// if user doesnt exist, create em
			if($user === null) $user = new User();
			
			$user->username = $username;
			$user->fullname = $fullname;
			$user->subjects = $subjects;
			$user->email = $email;
			$user->verified = 1;
			$user->save();
			// set role
			$user->roles()->sync(array($role));
		}else{

			// bad username, doesnt exist in ldap!
		}
	}
}