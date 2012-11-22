<?php

class Cleanup_Programmes {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Don't *ever* repeat yourself.
		$number = 1;

		while ($number <= 5) {
			$GLOBALS['field'] = "mod_$number";

			Schema::table('programmes', function($table){
				$field = $GLOBALS['field'];
				$table->drop_column($field . "_title");
				$table->drop_column($field . "_content");
			});

			Schema::table('programmes_revisions', function($table){
				$field = $GLOBALS['field'];
				$table->drop_column($field . "_title");
				$table->drop_column($field . "_content");
			});

			$number++;
		}

		Schema::table('programmes', function($table){
			// Main
    		$table->drop_column('title');
    		$table->drop_column('slug');
    		$table->drop_column('honours');
    		$table->drop_column('summary');

    		// Relations
    		$table->drop_column('school_id');
    		$table->drop_column('school_adm_id');
    		$table->drop_column('campus_id');

    		$table->drop_column('leaflet_ids');

    		$table->drop_column('related_school_ids',255);
			$table->drop_column('related_subject_ids',255);
			$table->drop_column('related_programme_ids',255);
		});

		Schema::table('programmes_revisions', function($table){

    		// Main
    		$table->drop_column('title');
    		$table->drop_column('slug');
    		$table->drop_column('honours');
    		$table->drop_column('summary');

    		// Relations
    		$table->drop_column('school_id');
    		$table->drop_column('school_adm_id');
    		$table->drop_column('campus_id');

    		$table->drop_column('leaflet_ids');

    		$table->drop_column('related_school_ids',255);
			$table->drop_column('related_subject_ids',255);
			$table->drop_column('related_programme_ids',255);

		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		// Don't repeat yourself.
		$number = 1;

		while ($number <= 5) {
			$GLOBALS['field'] = "mod_$number";

			Schema::table('programmes', function($table){
				$field = $GLOBALS['field'];
				$table->string($field . "_title");
				$table->text($field . "_content");
			});

			Schema::table('programmes_revisions', function($table){
				$field = $GLOBALS['field'];
				$table->string($field . "_title");
				$table->text($field . "_content");
			});

			$number++;
		}

		Schema::table('programmes', function($table){
			// Main
    		$table->string('title', 255);
    		$table->string('slug');
    		$table->string('honours');
    		$table->text('summary');

    		// Relations
    		$table->integer('school_id');
    		$table->integer('school_adm_id');
    		$table->integer('campus_id');

			$table->string('leaflet_ids',255);

			$table->string('related_school_ids',255);
			$table->string('related_subject_ids',255);
			$table->string('related_programme_ids',255);
		});

		// Add programmes revisions
		Schema::table('programmes_revisions', function($table){

    		// Main
    		$table->string('title', 255);
    		$table->string('slug');
    		$table->string('honours');
    		$table->text('summary');

    		// Relations
    		$table->integer('school_id');
    		$table->integer('school_adm_id');
    		$table->integer('campus_id');
    		
			$table->string('leaflet_ids',255);

			$table->string('related_school_ids',255);
			$table->string('related_subject_ids',255);
			$table->string('related_programme_ids',255);

		});
	}

}