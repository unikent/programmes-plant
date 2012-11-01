<?php

class Additional_UGProgrammes_Meta {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addFieldProgramme('Year Abroad','checkbox','','');
		$this->addFieldProgramme('Foundation Year','checkbox','','');
		$this->addFieldProgramme('Year In Industry','checkbox','','');
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{	
		DB::query('TRUNCATE subjects_meta');
		DB::query('TRUNCATE programmes_meta');
	}
	
	
	
	
	private function addFieldProgramme($title,$type,$hints,$options){
		$this->addField("programme", $title,$type,$hints,$options);
	}
	private function addFieldSubject($title,$type,$hints,$options){
		$this->addField("subject", $title,$type,$hints,$options);
	}
	private function addField($obj, $title,$type,$hints,$options){
	
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