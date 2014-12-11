<?php

class Ug_Deliveries {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes_ug', function($table){
			$table->drop_column('ari_code');
			$table->drop_column('ft_ari_code');
			$table->drop_column('current_ipo_pt');
			$table->drop_column('previous_ipo_pt');
			$table->drop_column('pos_code_44');
			$table->drop_column('parttime_mcr_code_87');
			$table->drop_column('fulltime_mcr_code_88');
		});

		Schema::table('programmes_revisions_ug', function($table){
			$table->drop_column('ari_code');
			$table->drop_column('ft_ari_code');
			$table->drop_column('current_ipo_pt');
			$table->drop_column('previous_ipo_pt');
			$table->drop_column('pos_code_44');
			$table->drop_column('parttime_mcr_code_87');
			$table->drop_column('fulltime_mcr_code_88');
		});

		DB::table("programmes_fields_ug")->where('colname','=','pos_code_44')->delete();
		DB::table("programmes_fields_ug")->where('colname','=','parttime_mcr_code_87')->delete();
		DB::table("programmes_fields_ug")->where('colname','=','fulltime_mcr_code_88')->delete();

		Schema::create('ug_programme_deliveries', function($table){
			$table->increments('id');
			$table->timestamps();

			$table->string('award', 255);

			$table->string('pos_code', 200);
			$table->string('attendance_pattern', 255);
			$table->string('mcr', 255);
			$table->integer('programme_id');

			$table->string('description', 255);

			$table->string('current_ipo', 4);
			$table->string('previous_ipo', 4);
			$table->string('ari_code', 12);
		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programmes_ug', function($table){
			$table->string('ari_code', 12);
			$table->string('ft_ari_code', 12);
			$table->string('current_ipo_pt', 4);
			$table->string('previous_ipo_pt', 4);
			$table->string('pos_code_44', 255);
			$table->string('parttime_mcr_code_87', 255);
			$table->string('fulltime_mcr_code_88', 255);
		});

		Schema::table('programmes_revisions_ug', function($table){
			$table->string('ari_code', 12);
			$table->string('ft_ari_code', 12);
			$table->string('current_ipo_pt', 4);
			$table->string('previous_ipo_pt', 4);
			$table->string('pos_code_44', 255);
			$table->string('parttime_mcr_code_87', 255);
			$table->string('fulltime_mcr_code_88', 255);
		});

		DB::table("programmes_fields_ug")->insert(array(
			'id' => 44, 
			'field_name' => 'POS Code', 
			'field_type' => 'text',
			'field_meta' => '',
			'prefill' => 0,
			'active' => 1,
			'view' => 1,
			'colname' => 'pos_code_44',
			'created_at' => '2012-12-17 16:18:55',
			'updated_at' => '2013-08-27 11:06:45',
			'programme_field_type' => '',
			'section' => 12,
			'order' => 2,
			'empty_default_value' => 0
		));

		DB::table("programmes_fields_ug")->insert(array(
			'id' => 87, 
			'field_name' => 'Part-time MCR code', 
			'field_type' => 'text',
			'field_meta' => '',
			'field_description' => '<p>The MCR code for the admissions system.</p>',
			'field_initval' => '2015',
			'prefill' => 0,
			'active' => 1,
			'view' => 1,
			'colname' => 'parttime_mcr_code_87',
			'created_at' => '2013-06-25 11:17:40',
			'updated_at' => '2013-08-27 11:04:06',
			'programme_field_type' => '',
			'section' => 12,
			'order' => 4,
			'empty_default_value' => 0
		));

		DB::table("programmes_fields_ug")->insert(array(
			'id' => 88, 
			'field_name' => 'Full-time MCR code', 
			'field_type' => 'text',
			'field_meta' => '',
			'prefill' => 0,
			'active' => 1,
			'view' => 1,
			'colname' => 'fulltime_mcr_code_88',
			'created_at' => '2013-08-06 13:57:45',
			'updated_at' => '2013-10-08 13:59:50',
			'programme_field_type' => '',
			'section' => 12,
			'order' => 3,
			'empty_default_value' => 0
		));

		Schema::drop('ug_programme_deliveries');
	}

}