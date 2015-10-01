<?php
use Laravel\Database\Schema;

class Create_Notes_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('notes', function($table){
            $table->increments('id');
            $table->integer('programme_id');
            $table->string('level');
            $table->text('note');
            $table->string('short_note');
			$table->timestamps();
        });
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('notes');
	}

}