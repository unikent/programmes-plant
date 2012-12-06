<?php
require_once dirname(dirname(__FILE__)) . '/lib/ControllerTestCase.php';

class TestCampuses_Controller extends ControllerTestCase
{

	/**
	 * Sets up database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
	}

	public function get_create_page()
	{
		return $this->get_html('campuses@create');
	}

	public function testCreateFormIsCorrect()
	{
		$html = $this->get_create_page();

		$fields = array(
			'description' => 'textarea',
			'identifier' => 'input',
			'title' => 'input',
			'address_1' => 'input',
			'address_2' => 'input',
			'address_3' => 'input',
			'town' => 'input',
			'email' => 'input',
			'phone' => 'input',
			'postcode' => 'input',
			'url' => 'input'
		);

		foreach($fields as $field => $tag)
		{
			$matcher = array(
			'tag' => $tag,
			'attributes' => array('name' => $field)
			);

			$this->assertTag($matcher, $html, "$field (form element type $tag) is missing from the create form");

			$matcher = array(
				'tag' => 'label',
				'attributes' => array('for' => $field)
			);

			$this->assertTag($matcher, $html, "$field (form element type $tag) is missing from the create form");
		}
	}

}