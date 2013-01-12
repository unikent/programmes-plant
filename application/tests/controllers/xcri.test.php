<?php
require_once dirname(dirname(__FILE__)) . '/lib/ControllerTestCase.php';

class TestXCRI_Controller extends ControllerTestCase {

	public function testget_indexShouldReturnAnXMLContentType() {}

	public function testget_indexShouldReturnA200WhenDataIsFoundForAYear() {}

	public function testget_indexShouldReturnA404WhenDataIsNotFoundForAYear() {}

	public function testget_indexShouldReturnDataForUndergraduatesForAYear() {}

	public function testget_indexShouldReturnDataForPostGraduateForAYear() {}

	public function testget_indexShouldReturnAValidXMLDocument() {}

	public function testget_indexShouldReturnAValidXCRIFeed() {}

	public function testget_indexShouldReflectChangesInDataInTheXCRIFeed() {}

	public function testget_indexShouldFillInAllRequiredElementsOfTheXCRIFeedOrError() {}

}