<?php
require_once dirname(dirname(__FILE__)) . '/lib/ControllerTestCase.php';

class TestProgrammes_Controller extends ControllerTestCase
{

	public function populate()
	{
		// Setup something we can edit.
		$input = array(
			'year' => '2014',
			'live' => '1',
			'created_by' => 'aa',
			'programme_title_1' => 'Test'
		);

		$programme = Programme::create($input);
		$programme->save();
	}

	public function tearDown()
	{
		$programmes = Programme::all();

		foreach ($programmes as $programme)
		{
			$programme->delete();
		}

		parent::tearDown();
	}

	public function testRemainOnTheSamePageWhenSavingProgrammes()
	{
		//Add a programme to the system.
		Programme::create(array('programme_title_1' => 'A programme', 'year'=> '2014', 'id' => 1));

		$edit_page = $this->get('programmes@edit', array('2014', 'ug', '1'));

		// Edit what we have populated.
		$input = array(
			'programme_id' => 1,
			'year' => '2014',
			'live' => '1',
			'created_by' => 'aa',
			'programme_title_1' => 'Test Change'
		);

		// Post to the edit page
		$next_page_after_edit_page_post = $this->post('programmes@edit', $input, array('2014', 'ug'));

		$this->assertEquals('302', $next_page_after_edit_page_post->status(), 'Page was not redirected.');
		$this->assertEquals('/2014/ug/programmes/edit/1', $this->get_location($next_page_after_edit_page_post), 'Page was not the same page.');
	}

}