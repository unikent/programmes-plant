<?php
require_once dirname(dirname(__FILE__)) . '/lib/ControllerTestCase.php';

class TestProgrammes_Controller extends ControllerTestCase
{

	public function populate()
	{
		// Setup something we can edit.
		$input = array(
			'year' => '2012',
			'live' => '1',
			'created_by' => 'aa',
			'published_by' => 'aa',
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
		$this->populate();

		$edit_page = $this->get('programmes@edit', array('2012', 'ug', '1'));

		// Edit what we have populated.
		$input = array(
			'programme_id' => 1,
			'year' => '2012',
			'live' => '1',
			'created_by' => 'aa',
			'published_by' => 'aa',
			'programme_title_1' => 'Test Change'
		);

		// Post to the edit page
		$next_page_after_edit_page_post = $this->post('programmes@edit', $input, array('2012', 'ug'));

		$this->assertEquals('302', $next_page_after_edit_page_post->status(), 'Page was not redirected.');
		$this->assertEquals('/programmes/edit/1', $this->get_location($next_page_after_edit_page_post), 'Page was not the same page.');
	}

}