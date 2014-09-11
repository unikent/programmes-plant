<?php

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

		$document = new DOMDocument;
		@$document->loadHTML( $html );
		$xpath    = new DOMXPath( $document );

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
			// Ensure input/textarea
			$this->assertEquals(
				1, $xpath->query( "//{$tag}[@name = \"{$field}\"]" )->length,
				"$field (form element type $tag) is missing from the create form"
		 	);
			// ensure label
			$this->assertEquals(
				1, $xpath->query( "//label[@for = \"{$field}\"]" )->length,
				"$field (form element type $tag) is missing from the create form"
		 	);

		}
	}

}