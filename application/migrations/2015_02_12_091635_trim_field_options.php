<?php

class Trim_Field_Options {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// for UG select fields
		$ug_fields = UG_ProgrammeField::where('field_type', '=', 'select')->get();

		foreach ($ug_fields as $ug_field) {
			DB::query("UPDATE programmes_revisions_ug SET `{$ug_field->colname}` = trim(`{$ug_field->colname}`)");
			DB::query("UPDATE programmes_ug SET `{$ug_field->colname}` = trim(`{$ug_field->colname}`)");
		}

		// for PG select fields
		$pg_fields = PG_ProgrammeField::where('field_type', '=', 'select')->get();

		foreach ($pg_fields as $ug_field) {
			DB::query("UPDATE programmes_revisions_pg SET `{$ug_field->colname}` = trim(`{$ug_field->colname}`)");
			DB::query("UPDATE programmes_pg SET `{$ug_field->colname}` = trim(`{$ug_field->colname}`)");
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		// We dont need this to be reversible
	}

}