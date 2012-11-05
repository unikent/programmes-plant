<?php

class Create_Globals {

	/**
	 * Create the globals tables.
	 * 
	 * This table stores global variables to be used by the XCRI feed.
	 * 
	 * There are three tables.
	 * 
	 * First the globals table that stores the current revision of the global variable.
	 * 
	 * Second the globals_meta table. This stores additional items that can be added to the globals table. It is polled from time to time to produce new columns in the globals table.
	 * 
	 * Third the globals_revisions table. This stores revisions if the globals table. It can also store all additional meta fields added.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the globals table
		Schema::table('globals', function($table){
			$table->create();
			$table->increments('id');

			$table->string('year',4);
			$table->string('institution',255);

			$table->string('created_by',10);
			$table->string('published_by', 10);
			$table->timestamps();

		});

		// Create the globals_revisions table
		Schema::table('globals_revisions', function($table){
			$table->create();
			$table->increments('id');
			$table->integer("global_id");
			$table->string('year', 4);
			$table->string('institution', 255);

			$table->string('created_by', 10);
			$table->string('status', 15);
			$table->timestamps();

		});

		// Create the globals_meta_table
		Schema::table('globals_meta', function($table){
			$table->create();
    		$table->increments('id');

    		$table->string('field_name');
    		$table->string('field_type');
    		$table->string('field_meta');
    		$table->string('field_description');
    		$table->string('field_initval');

    		$table->integer("prefill")->default('0');
			$table->text("placeholder");

    		$table->integer('active');
    		$table->integer('view');
    		$table->string('colname');

    		$table->timestamps();
		});

		// Add some fields in
		$this->add_field('global','KIS Instition ID','text','','');
		$this->add_field('global','Apply Content','textarea','','');
		$this->add_field('global','Fees Content','textarea','','');
		$this->add_field('global','Additional entry requirment information','textarea','','');
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('globals');
		Schema::drop('globals_meta');
		Schema::drop('globals_revisions');
	}

	/**
	 * Adds a field to a meta table.
	 * 
	 * This allows one to prepopulate the meta tables with some fields.
	 * 
	 * @param string $obj The object which we are creating. For example here ''
	 * @param string $title The title of the field.
	 * @param string $type The type of field.
	 * @param string $hints The hints for the field.
	 * @param string $options The options, particularly used when the field type is select.
	 */
	private function add_field($obj, $title, $type, $hints, $options)
	{
		$colname = Str::slug($title, '_');

		$model = $obj.'Meta';
		$subject = new $model;
	    $subject->field_name = $title;
	    $subject->field_type = $type;
	    $subject->field_description = $hints;
	    $subject->field_meta = $options;
	    $subject->field_initval =  '';
	    $subject->active = 1;
	    $subject->view = 1;
	    $subject->save();
		$colname .= '_'.$subject->id;
		$subject->colname = $colname;
		$subject->save();
		
		Schema::table($obj.'s', function($table) use ($colname, $type){
			if($type=='textarea'){
				$table->text($colname);
			}else{
				$table->string($colname,255);
			}		
		});

		Schema::table($obj.'s_revisions', function($table) use ($colname, $type){
			if($type=='textarea'){
				$table->text($colname);
			}else{
				$table->string($colname,255);
			}
		});
	}
}