<?php

class Field_Meta {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//Subjects to initiate
		$this->addFieldSubject('Fact Box','textarea','','');
		$this->addFieldSubject('Student Profile','text','A link to the student profile page','');
		$this->addFieldSubject('Derpicated Text','textarea','','');
		//Programmes to initiate
		$this->addFieldProgramme('UCAS Code','text','','');
		$this->addFieldProgramme('POS Code','text','','');
		$this->addFieldProgramme('KIS Course ID','text','','');
		
		$this->addFieldProgramme('Duration','text','','');
		$this->addFieldProgramme('Fee Information','textarea','','');
		
		$this->addFieldProgramme('Teaching and Assessment Content','textarea','','');
		$this->addFieldProgramme('Careers content','textarea','','');
		$this->addFieldProgramme('Entry Requirments','textarea','','');
		$this->addFieldProgramme('Offer Levels','textarea','','');
		$this->addFieldProgramme('Required Subjects','textarea','','');
		$this->addFieldProgramme('Additional Entry Requirments','textarea','','');
		
		$this->addFieldProgramme('Apply Content','textarea','','');
		$this->addFieldProgramme('Futher Information Intro','textarea','','');
		$this->addFieldProgramme('Enquires','textarea','','');
		
		$this->addFieldProgramme('Depricated text','textarea','','');
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