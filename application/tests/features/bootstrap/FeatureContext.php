<?php
use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;

/**
 * Load third-party libraries.
 */
// Require Mink
require_once 'mink/autoload.php';

// Require PHPUnit (if available)
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';


// --------------------------------------------------------------
// Define the directory separator for the environment.
// --------------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);

// --------------------------------------------------------------
// Set the core Laravel path constants.
// --------------------------------------------------------------
require 'paths.php';

// --------------------------------------------------------------
// Bootstrap the Laravel core.
// --------------------------------------------------------------

require path('sys').'core.php';

// --------------------------------------------------------------
// Start the default bundle.
// --------------------------------------------------------------
Laravel\Bundle::start(DEFAULT_BUNDLE);


ini_set('display_errors', 0);

/**
 * Features context.
 */
class FeatureContext extends Behat\Mink\Behat\Context\MinkContext
{

	public function __construct(array $parameters) {
		if (method_exists(get_parent_class('FeatureContext'), '__construct')) {
			parent::__construct($parameters);
		}
		$this->useContext('web', new WebContext);
		$this->useContext('email', new EmailContext);
		$this->useContext('app', new AppContext);
		$this->useContext('record', new RecordContext);
	}

	public function locatePath($path,$prefix_base_url = true) {
		$startUrl = rtrim($this->getParameter('base_url'), '/') . '/';
		$path = require __DIR__."/paths.php";
		return 0 !== strpos($path, 'http') && $prefix_base_url ? $startUrl . ltrim($path, '/') : $path;
	}

	/**
	* @When /^I follow the "([^"]*)" action link for "([^"]*)"$/
 	*/
	public function iFollowTheActionLinkFor($argument1, $argument2) {
		$content = $this->getSession()->getPage()->find('css', "tr:contains({$argument2})");
		$link = $content->findLink($argument1);
		assertNotNull($link);
		$link->click();
	}

	/**
	* @Then /^"([^"]*)" in "([^"]*)" should be selected$/
	*/
	public function inShouldBeSelected($optionValue, $select) {
		$selectElement = $this->getSession()->getPage()->find('named', array('select', "\"{$select}\""));
		$optionElement = $selectElement->find('named', array('option', "\"{$optionValue}\""));

		//it should have the attribute selected and it should be set to selected
		assertTrue($optionElement->hasAttribute("selected"));
		assertTrue($optionElement->getAttribute("selected") == "selected");
	}

	/**
	* @Given /^"([^"]*)" in "([^"]*)" should not be selected$/
	*/
	public function inShouldNotBeSelected($optionValue, $select) {
		$selectElement = $this->getSession()->getPage()->find('named', array('select', "\"{$select}\""));
		$optionElement = $selectElement->find('named', array('option', "\"{$optionValue}\""));

		//it should have the attribute selected and it should be set to selected
		assertFalse($optionElement->hasAttribute("selected"));
	}


	/**
	* @When /^I type "([^"]*)" within "([^"]*)"$/
 	*/
	public function iTypeWithin($input, $selector) {
		$el = $this->getSession()->getPage()->find('css', $selector);

		// Supports input of character codes, by placing them in square brackets.
		preg_match_all('/(\[.{1,2}\])|(.){1}/', $input, $chars);
		foreach($chars[0] as $char){
			if(strstr($char, '[') && strstr($char, ']')){
				$char = str_replace(']', '', str_replace('[', '', $char));
				$char = (int) $char;
			}

			$r = $el->keyPress($char);
		}
	}


	/**
	* @Then /^"([^"]*)" should be visible$/
 	*/
	public function shouldBeVisible($selector) {
		$el = $this->getSession()->getPage()->find('css', $selector);

		$style = '';
		if(!empty($el)){
			$style = preg_replace('/\s/', '', $el->getAttribute('style'));
		} else {
        	throw new Exception("Element ({$selector}) not found");
		}

		assertFalse(false !== strstr($style, 'display:none'));
	}

	/**
	* @Then /^"([^"]*)" should not be visible$/
 	*/
	public function shouldNotBeVisible($selector) {
		$el = $this->getSession()->getPage()->find('css', $selector);

		$style = '';
		if(!empty($el)){
			$style = preg_replace('/\s/', '', $el->getAttribute('style'));
		} else {
        	throw new Exception("Element ({$selector}) not found");
		}

		assertTrue(false !== strstr($style, 'display:none'));
	}

	/**
	* @Then /^should see valid JSON$/
 	*/
	public function shouldSeeValidJSON() {
		$json = $this->getSession()->getPage()->getContent();
		assertTrue($json !== 0 && (false !== json_decode($json)));
	}

	/**
	* @Then /^the JSON should contain "(.*)"$/
 	*/
	public function theJSONShouldContain($contains) {
		$json = $this->getSession()->getPage()->getContent();
		assertTrue(false !== strstr($json, $contains));
	}

	/**
	* @Then /^the JSON should not contain "(.*)"$/
	*/
	public function theJSONShouldNotContain($contains) {
		$json = $this->getSession()->getPage();
		assertFalse(false !== strstr($json, $contains));
	}

	/**
	 * @AfterSuite
	 */
	public static function beep(Behat\Behat\Event\SuiteEvent $event) {
		$params = $event->getContextParameters();
		$result = $event->getLogger()->getFeaturesStatuses();
		if (isset($params['announce_completion']) && $params['announce_completion']) {
			if ($result['failed'] || $result['undefined'] ||  $result['skipped']) {
				exec('D:\saystatic.exe "Behat failed. Oh no!"');
			} else {
				exec('D:\saystatic.exe "Behat Succeeded. Wooop!"');
			}
		}
	}

	/**
	 * @BeforeSuite
	 */
	public static function createPasswordAndEmailDirs(Behat\Behat\Event\SuiteEvent $event) {
		if (!file_exists("/tmp/emails")) {
			mkdir("/tmp/emails",0777,true);
			chgrp("/tmp/emails","_www");
			chmod("/tmp/emails", 0775);
		}
		if (!file_exists("/tmp/passwords")) {
			mkdir("/tmp/passwords",0777,true);
			chgrp("/tmp/passwords","_www");
			chmod("/tmp/passwords", 0775);
		}
	}


	/**
	 * Checks, that current page PATH is equal to specified.
	 */
	public function assertPageAddress($page) {
		$expected = parse_url($this->locatePath($page), PHP_URL_PATH);
		$expected = preg_replace('/^\/[^\.\/]+\.php/', '', $expected);

		$actual = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);
		$actual = preg_replace('/^\/[^\.\/]+\.php/', '', $actual);

		try {
			try {
				assertEquals($expected, $actual);
			} catch(Exception $e) {
				@assertRegExp($expected, $actual);
			}
		} catch (Exception $e) {
			$message = sprintf('Current page is "%s", but "%s" expected', $actual, $expected);
			throw new \Behat\Mink\Exception\ExpectationException($message, $this->getSession(), $e);
		}
	}

	/**
	 * 
	 */
	public function assertPageContainsText($text) {

		$expected = str_replace('\\"', '"', $text);

		try {
			$actual   = $this->getSession()->getPage()->getText();
		} catch (Exception $e) {
			$actual   = $this->getSession()->getPage()->getContent();
		}

		try {
			assertContains($expected, $actual);
		} catch (AssertException $e) {
			$message = sprintf('The text "%s" was not found anywhere in the text of the current page', $expected);
			throw new ResponseTextException($message, $this->getSession(), $e);
		}

	}

}
