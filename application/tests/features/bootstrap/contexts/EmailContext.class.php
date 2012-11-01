<?php

use Behat\Behat\Context\BehatContext;
use Behat\Mink\Exception\ExpectationException;

class EmailException extends Exception {}

class EmailContext extends BehatContext {

	/**
	 * @Given /^"(?P<address>[^"]*)" has no emails?$/
	 */
	public function userHasNoEmails($address) {
		if(getenv("OS")!=="Windows_NT") {
			exec("rm -rf /tmp/emails/{$address}*");
		}
	}

	/**
	 * @Given /^there are no emails$/
	 */
	public function thereAreNoEmails() {
		if(getenv("OS")!=="Windows_NT") {
			exec("rm -rf /tmp/emails/*");
		}
	}

	/**
	 * @Then /^"(?P<email>(?:[^"]|\\")*)" should receive an email$/
	 */
	public function shouldReceiveAnEmail($email) {
		foreach (scandir("/tmp/emails",1) as $value) {
			if (strpos($value,$email) === 0) {
				$this->getMainContext()->email_address = $email;
				return;
			}
		}
		$message = sprintf('"%s" hasn\'t recieved any emails', $email);
		throw new EmailException($message, $this->getMainContext()->getSession());
	}

	/**
	 * @When /^(?:|I )open the email$/
	 */
	public function openEmail() {
		foreach (scandir("/tmp/emails",1) as $value) {
			if (strpos($value,$this->getMainContext()->email_address) === 0) {
				$this->getMainContext()->current_email = file_get_contents("/tmp/emails/".$value);
				return;
			}
		}
		$message = sprintf('"%s" hasn\'t recieved any emails', $email);
		throw new EmailException($message, $this->getMainContext()->getSession());
	}

	/**
	 * @Then /^(?:|I )should see "(?P<text>[^"]*)" in the email subject$/
	 */
	public function assertSubjectContains($text) {
		assertRegExp("/Subject: ".preg_quote($text)."/",$this->getMainContext()->current_email,"Email subject is not containt the text \"$text\"");
	}

	/**
	 * @Then /^(?:|I )should see "(?P<text>[^"]*)" in the email body/
	 */
	public function assertBodyContains($text) {
		assertRegExp("/.*".preg_quote($text,"/")."/",$this->getMainContext()->current_email,"Email body does not contain the text \"$text\"");
	}

	/**
	 * @When /^(?:|I )follow the first link in the email/
	 */
	public function followFirstLink() {
		assertGreaterThanOrEqual(1,$match = preg_match("/href=('|\")([^\"']*)('|\")/",$this->getMainContext()->current_email,$matches));
		$this->getMainContext()->visit($matches[2]);
	}

	/**
	 * @Then /^the email should be plain text$/
	 */
	public function assertPlainText() {
		assertRegExp("/Content\-type\: text\/plain/",$this->getMainContext()->current_email);
	}

	/**
	 * @Then /^the email should be html$$/
	 */
	public function assertHtml() {
		assertRegExp("/Content\-type\: text\/html/",$this->getMainContext()->current_email);
	}

}