<?php

class Add_More_cols {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addFieldProgramme('New Programme','checkbox','Display "new" icon beside this programme','');
		
		$this->addFieldProgramme('Can study Part-Time','checkbox','','','true');
		$this->addFieldProgramme('Can study Full-Time','checkbox','','','true');

		

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{	
	
	}
	
	private function addFieldProgramme($title,$type,$hints,$options,$init=''){
		$this->addField("programme", $title,$type,$hints,$options,$init);
	}
	private function addFieldSubject($title,$type,$hints,$options,$init=''){
		$this->addField("subject", $title,$type,$hints,$options,$init);
	}
	private function addField($obj, $title,$type,$hints,$options,$init = ''){
	
				$colname = Str::slug($title, '_');
	
				$model = $obj.'Meta';
				$subject = new $model;
                $subject->field_name = $title;
                $subject->field_type = $type;
                $subject->field_description = $hints;
                $subject->field_meta = $options;
                $subject->field_initval =  $init;
				if($init !='')$subject->prefill = 1;
                $subject->active = 1;
                $subject->view = 1;
                $subject->save();
				$colname .= '_'.$subject->id;
				$subject->colname = $colname;
				$subject->save();
				
				
				Schema::table($obj.'s', function($table) use ($colname, $type, $init){
					if($type=='textarea'){
						$table->text($colname);
					}else{
						$table->string($colname,255)->default($init);
					}		
				});
				Schema::table($obj.'s_revisions', function($table) use ($colname, $type, $init){
					if($type=='textarea'){
						$table->text($colname);
					}else{
						$table->string($colname,255)->default($init);
					}
				});
	}

}