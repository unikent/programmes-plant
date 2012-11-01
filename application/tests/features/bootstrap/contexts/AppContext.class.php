<?php

use Behat\Behat\Context\BehatContext;

class AppContext extends BehatContext {

	/**
	 * @Given /^a validated user "([^"]*)"$/
	 */
	public function givenValidatedUser($username) {

		create_user_obj(make_user_obj(array(
			'username'=>$username,
			'token'=>'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
			'tokenexpires'=>date('Y-m-d H:i:s',time()+60*60),
		)));

		$this->getMainContext()->getSession()->visit($this->getMainContext()->locatePath("the password hash step1 with hash(AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA)"));

	}


	/**
	 * @Given /^a validated user "([^"]*)" with entered alt email "([^"]*)"$/
	 */
	public function givenValidatedUserWithAltEmail($username, $alt_email) {

		create_user_obj(make_user_obj(array(
			'username'=>$username,
			'token'=>'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
			'tokenexpires'=>date('Y-m-d H:i:s',time()+60*60),
			'altemail'=>$alt_email,
		)));

		$this->getMainContext()->getSession()->visit($this->getMainContext()->locatePath("the password hash step1 with hash(AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA) and alt email($alt_email)"));

	}

	/**
	 * @Given /^a validated user "([^"]*)" with entered alt email "([^"]*)" and filed alt email "([^"]*)"$/
	 */
	public function givenValidatedUserWithAltEmailAndFiledAltEmail($username, $alt_email,$alt_email2) {

		create_user_obj(make_user_obj(array(
			'username'=>$username,
			'token'=>'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
			'tokenexpires'=>date('Y-m-d H:i:s',time()+60*60),
			'altemail'=>$alt_email2,
		)));

		$this->getMainContext()->getSession()->visit($this->getMainContext()->locatePath("the password hash step1 with hash(AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA) and alt email($alt_email)"));

	}

	private function getLdap(){
		$ldap_opt = option('LDAP_settings');
		//Create real LDAP Obj

		$ldapObj = new LDAP($ldap_opt['host'], $ldap_opt['port']);
		$ldapObj->setBaseUser($ldap_opt['dn'],$ldap_opt['pass']);
		$ldapObj->setBaseRDN($ldap_opt['base_dn']);	
		return $ldapObj;
	}


	/**
	 * @Then /^"([^"]*)"\'s password should be "([^"]*)"$/
	 */
	public function assertPassword($login, $password) {
		assertEquals($password,file_get_contents("/tmp/passwords/$login.txt"));	
	}

	/**
	 * @Given /^user "([^"]*)" has a password of "([^"]*)"$/
	 */
	public function setPassword($login, $password) {
		if (!option('fake_ldap')) {
			//$ldap = $this->getLDAP();
			//$ldap->updatePassword('at369', $password);
		}
		file_put_contents("/tmp/$login.txt",$password);
	}

	/**
	 * @When /^I answer the question with:$/
	 */
	public function answerQuestion($table) {

		$rows = $table->getRowsHash();

		foreach ($rows as $key => $value) {
			$body = strip_tags($this->getMainContext()->getSession()->getPage()->getContent());
			if (strpos($body,$key)!==false) {
				$this->getMainContext()->fillField("answer",$value);
			}
		}

	}

	/**
	 * @Then /^I should see a helpdesk hash for username\(([^)]+)\) alt_email\(([^)]+)\) in the email body$/
	 */
	public function assertEmailContainsHash($username, $alt_email) {
		$user = find_user_by_username($username);
		$hash = hash('sha1',"{$username}{$alt_email}AN UNGUESSABLE STRING{$user->adminhashtime}");
		assertRegExp('/'.preg_quote($hash,"/").'/', strip_tags($this->getMainContext()->current_email));
	}

}