<?php

use Behat\Behat\Context\BehatContext;

class WebContext extends BehatContext {

	/**
	 * Checks, that current page PATH is not equal to specified.
	 *
	 * @Then /^(?:|I )should not be on "(?P<page>[^"]+)"$/
	 */
	public function assertPageNotAddress($page)
	{
		$expected = parse_url($this->getMainContext()->locatePath($page), PHP_URL_PATH);
		$expected = preg_replace('/^\/[^\.\/]+\.php/', '', $expected);

		$actual = parse_url($this->getMainContext()->getSession()->getCurrentUrl(), PHP_URL_PATH);
		$actual = preg_replace('/^\/[^\.\/]+\.php/', '', $actual);

		try {
			assertNotEquals($expected, $actual);
		} catch (AssertException $e) {
			$message = sprintf('Current page is "%s", but "%s" expected', $actual, $expected);
			throw new ExpectationException($message, $this->getMainContext()->getSession(), $e);
		}
	}


	/**
	 * Override for Behat's assertPageContainsText that will check all elements for text.
	 *
	 * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in an? "(?P<element>[^"]*)" element$/
	 */
	public function assertPageContainsTextWithin($text,$selector) {

		$nodes = $this->getMainContext()->getSession()->getPage()->findAll('css', $selector);
		$text = str_replace('\\"', '"', $text);

		if (!count($nodes)) {
			throw new Behat\Mink\Exception\ElementNotFoundException(
				$this->getMainContext()->getSession(), 'element', 'css', $element
			);
		}

		foreach ($nodes as $node) {
			try {
				assertContains($text, $node->getText());
				$found = true;
				break;
			} catch (Exception $e) {
				$found=false;
			}
		}
		if (!$found) {
			$message = sprintf('The text "%s" was not found in the text of the elements matching css "%s"', $text, $element);
				throw new Behat\Mink\Exception\ElementTextException($message, $this->getSession(), $node, $e);
		}

	}


	/**
	 * @Then /^I should see a link to "(?P<path>(?:[^"]|\\")*)" with text "(?P<text>(?:[^"]|\\")*)"$/
	 */
	public function iShouldSeeALinkToWithText($path, $text) {
		assertTrue(null !== $this->getMainContext()->getSession()->getPage()->find('css',"a[href$='".$this->getMainContext()->locatePath($path,false)."']:contains('$text')"));
	}

	/**
	 * @Then /^I should see the pattern "([^"]*)"$/
	 */
	public function assertThePattern($pattern) {
	  assertRegExp($pattern,$this->getMainContext()->getSession()->getPage()->getContent(),"Could not find \"{$pattern}\" in the page's body.");
	}

	/**
	 * @Given /^(?:|I )print location$/
	 */
	public function printLocation() {
		$this->printDebug(
			$this->getMainContext()->getSession()->getCurrentUrl()
		);
	}

	/**
	 * @When /^(?:|I )wait (\d+)$/
	 */
	public function sleep($time) {
		sleep($time);
	}



}