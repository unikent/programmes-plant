<?php

class TestDelivery extends ModelTestCase 
{

	public static function tear_down()
	{
		Cache::flush();
		parent::tear_down();
	}

	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
		static::clear_models();
		static::add_programmes();
		static::add_sample_deliveries();
	}

	// public function testDeleteClearsProgrammeOutputCaches()
	// {
	// 	$programme = UG_Programme::find(1);
	// 	$revision = $programme->get_revision(1);
	// 	$programme->make_revision_live($revision);

	// 	$this->assertNotEmpty(Cache::get("api-output-ug/programme-2014-1"));

	// 	$delivery = UG_Delivery::find(1);
	// 	$delivery->delete();

	// 	$this->assertEmpty(Cache::get("api-output-ug/programme-2014-1"));
	// }

	public function testGenerateAPIDataGeneratesCorrectCache()
	{
		UG_Delivery::generate_api_data();
		$ug_deliveries = Cache::get('api-ug_delivery');
		$this->assertNotEmpty($ug_deliveries);
		$this->assertEquals($ug_deliveries[1]['pos_code'], 'APPBEHANAL:GDIP');

		PG_Delivery::generate_api_data();
		$pg_deliveries = Cache::get('api-pg_delivery');
		$this->assertNotEmpty($pg_deliveries);
		$this->assertEquals($pg_deliveries[3]['mcr'], 'PABI000101MS-PD');
	}

	public function testGenerateProgrammeDeliveriesGetsAllProgrammeDeliveries()
	{
		$deliveries = PG_Delivery::generate_programme_deliveries(2, 2015, 2);
		$this->assertNotEmpty($deliveries);
		$this->assertEquals(count($deliveries), 5);
	}

	public function testGenerateProgrammeDeliveriesGetsAllProgrammeDeliveriesWithoutProgrammeID()
	{
		$deliveries = PG_Delivery::generate_programme_deliveries(2, 2015);
		$this->assertNotEmpty($deliveries);
		$this->assertEquals(count($deliveries), 5);
	}

	public function testGetProgrammeDeliveriesCreatesProgrammeDeliveriesCache()
	{
		$deliveries = UG_Delivery::get_programme_deliveries(1, 2014);
		$deliveries_cache = Cache::get("ug-deliveries.1-2014");
		$this->assertNotEmpty($deliveries_cache);
		for ($i=0; $i < count($deliveries); $i++) { 
			$this->assertEquals($deliveries[$i]['id'], $deliveries_cache[$i]['id']);
		}
	}

	public static function add_sample_deliveries()
	{
		$ug_deliveries = array(
			array('award' => '24','pos_code' => 'APPBEHANAL:GDIP','attendance_pattern' => 'full-time','mcr' => 'UABA000101GD-FD','programme_id' => '1','description' => 'Applied Behaviour Analysis - GDip - Full-time at Canterbury','current_ipo' => '0002','previous_ipo' => '','ari_code' => 'MCR000001359'),
			array('award' => '2','pos_code' => 'ACCF-ECON:BA','attendance_pattern' => 'part-time','mcr' => 'UACFECO201BA-PD','programme_id' => '1','description' => 'Accounting and Finance and Economics - BA (Hons) - part-time at Canterbury','current_ipo' => '0002','previous_ipo' => '','ari_code' => 'MCR000001366')
		);

		$pg_deliveries = array(
			array('award' => '5','pos_code' => 'ACTSCIAP:MSC-T','attendance_pattern' => 'full-time','mcr' => 'PAAS000101MS-FD','programme_id' => '2','description' => 'Applied Actuarial Science - MSc - full-time at Canterbury','current_ipo' => '0001','previous_ipo' => '','ari_code' => 'MCR000000013'),
			array('award' => '5','pos_code' => 'ACTSCIAP:MSC-T','attendance_pattern' => 'part-time','mcr' => 'PAAS000101MS-PD','programme_id' => '2','description' => 'Applied Actuarial Science - MSc - part-time at Canterbury','current_ipo' => '0001','previous_ipo' => '','ari_code' => 'MCR000000014'),
			array('award' => '5','pos_code' => 'APPBEHAN(IDA):MSC-T','attendance_pattern' => 'part-time','mcr' => 'PABI000101MS-PD','programme_id' => '2','description' => 'Applied Behaviour Analysis (Intellectual and Developmental Disability) - MSc - part-time at Canterbury','current_ipo' => '0002','previous_ipo' => '','ari_code' => 'MCR000000019'),
			array('award' => '6','pos_code' => 'APPBEHAN(IDA):PCRT-T','attendance_pattern' => 'full-time','mcr' => 'PABI000101PC-FD','programme_id' => '2','description' => 'Applied Behaviour Analysis (Intellectual and Developmental Disability) - PCert - full-time at Canterbury','current_ipo' => '0002','previous_ipo' => '','ari_code' => 'MCR000000020'),
			array('award' => '14','pos_code' => 'APPBEHAN(IDA):PDIP-T','attendance_pattern' => 'full-time','mcr' => 'PABI000101PP-FD','programme_id' => '2','description' => 'Applied Behaviour Analysis (Intellectual and Developmental Disability) - PDip - full-time at Canterbury','current_ipo' => '0002','previous_ipo' => '','ari_code' => 'MCR000000022')
		);

		for ($i=0; $i < count($ug_deliveries); $i++) { 
			static::add_delivery($ug_deliveries[$i], 'UG');
		}
		for ($i=0; $i < count($pg_deliveries); $i++) { 
			static::add_delivery($pg_deliveries[$i], 'PG');
		}
	}

	public static function add_programmes()
	{
		UG_Programme::create(array('year' => '2014', 'programme_title_1' => 'Thing 2014', 'id' => 1, 'instance_id' => 1));
		PG_Programme::create(array('year' => '2015', 'programme_title_1' => 'Thing 2015', 'id' => 2, 'instance_id' => 2));
	}

	public static function add_delivery($delivery_data, $level)
	{
		$class = $level . "_Delivery";
		$class::create($delivery_data);
	}


}