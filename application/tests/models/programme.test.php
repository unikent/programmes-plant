<?php

class TestProgramme extends ModelTestCase {

	public function tearDown()
	{
		$this->clear_models(array('ProgrammeRevision', 'Programme'));
	}

	public function testXcrifyReturnsAStdClass() {}

	public function testXcrifyReturnsAValidFlattenedVersionOfTheObject() {}

	public function testget_under_reviewReturnsRevisionsUnderReview()
	{
		$revision = ProgrammeRevision::create(array('status' => 'under_review'));
		$revision->save();

		$under_review = Programme::get_under_review();

		$this->assertCount(1, $under_review);
		$this->assertTrue(is_a($under_review[0], 'ProgrammeRevision'));
	}

}