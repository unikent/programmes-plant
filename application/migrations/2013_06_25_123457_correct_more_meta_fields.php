<?php

// Namespace meta values in programme feilds to become PG_award vs award for example

class Correct_More_Meta_Fields {

	protected $meta_values = array('Award','Subject','Leaflet','SubjectCategory');

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Change meta links for the following tables (for both UG and PG)
		foreach($this->meta_values as $value){
			$this->meta_replace('programmes_fields_ug', $value, 'UG_'.$value);
			$this->meta_replace('programmes_fields_pg', $value, 'PG_'.$value);
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		// Change meta links for the following tables (for both UG and PG)
		foreach($this->meta_values as $value){
			$this->meta_replace('programmes_fields_ug', 'UG_'.$value, $value);
			$this->meta_replace('programmes_fields_pg', 'PG_'.$value, $value);
		}
	}

	// Perform a replace on DB
	private function meta_replace($table, $current_value, $new_value){
		DB::table($table)->where('field_meta','=', $current_value)->update(array('field_meta' => $new_value));
	}
}