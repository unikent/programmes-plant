<?php
class User extends Eloquent {
	public static $timestamps = true;

	public function roles()
	{
		return $this->has_many_and_belongs_to('Role');
	}
	public function set_password($password)
	{
		$this->set_attribute('password', Hash::make($password));
	}
	public function get_created_at(){
		return date('j-M-y H:i',strtotime($this->get_attribute('created_at')));
	}
	public function get_updated_at(){
		return date('j-M-y H:i',strtotime($this->get_attribute('updated_at')));
	}
	public function get_fullname(){
		return $this->get_attribute('first_name').' '.$this->get_attribute('last_name');
	}
	public function get_is_admin(){
		return ( $this->get_attribute('admin') === 1 ? true : false );
	}
}