<?php

class Create_Programmes_Pg {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

		// Add programmes
		Schema::create('programmes_pg', function($table){

			$table->engine = "MyISAM";

    		$table->increments('id');
    		$table->timestamps();
    		$table->string('year', 4);
			$table->integer('live');
			$table->boolean('hidden');
			$table->text("locked_to");
			$table->integer('instance_id');
			$table->integer('live_revision');
			$table->integer('current_revision');
			$table->string('created_by', 10);

			// indexes
			$table->index('year');
			$table->index('instance_id');
			$table->index('hidden');

		});

		// Add programmes revisions
		Schema::create('programmes_revisions_pg', function($table){

			$table->engine = "MyISAM";

    		$table->increments('id');
    		$table->timestamps();
    		$table->string('year', 4);
    		$table->boolean('hidden');
    		$table->integer('instance_id');
			$table->string('edits_by');
			$table->string('made_live_by');
			$table->date('published_at');
			$table->string("status");
			$table->integer("programme_id");
			$table->integer('under_review');

			// indexes
			$table->index('year');
			$table->index('programme_id');
			$table->index('status');
			$table->index('instance_id');

		});

		// add programme settings and their revisions
		Schema::create('programme_settings_pg', function($table){

			$table->increments('id');
			$table->string('year', 4);
			$table->string('created_by', 10);
			$table->timestamps();
			$table->integer('instance_id');
			$table->integer('live_revision');
			$table->integer('current_revision');
			$table->integer('live');

			// indexes
			$table->index('instance_id');
		});

		Schema::create('programme_settings_revisions_pg', function($table){

			$table->increments('id');
			$table->integer('programme_setting_id');
			$table->string('year', 4);
			$table->string('status', 15);
			$table->timestamps();
			$table->integer('instance_id');
			$table->string('edits_by');
			$table->string('made_live_by');
			$table->date('published_at');

			// indexes
			$table->index('year');
			$table->index('status');
		});

		// Add programme fields
		Schema::create('programmes_fields_pg', function($table){

			$table->increments('id');
			$table->string('field_name');
			$table->string('field_type');
			$table->string('field_meta');
			$table->string('field_description');
			$table->string('field_initval');
			$table->text('placeholder');
			$table->integer('prefill');
			$table->integer('active');
			$table->integer('view');
			$table->string('colname');
			$table->timestamps();
			$table->integer('programme_field_type');
			$table->integer('section');
			$table->integer('order');
			$table->integer('empty_default_value');
			$table->string('limit');

			// indexes
			$table->index('section');
		});
		
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('programmes_pg');
		Schema::drop('programmes_revisions_pg');
		Schema::drop('programme_settings_pg');
		Schema::drop('programme_settings_revisions_pg');
		Schema::drop('programmes_fields_pg');
	}

}