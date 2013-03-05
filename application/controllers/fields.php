<?php

class Fields_Controller extends Admin_Controller {

	public $restful = true;
	public $views = 'fields';

	public $required_permissions = array('configure_fields');

	/**
	 * Display the fields index page.
	 */
	public function get_index($type)
	{
		$model = $this->model;
		$fields = $model::select('*');
		
		if($this->where_clause){
			foreach ($this->where_clause as $clause) {
				$fields = $fields->or_where($clause[0], $clause[1], $clause[2]);
			}
		}
		
		// Sections
		$sections = "";

		// Only show sections on the programme fields lising page ie we don't want them for globalsetting fields or programmesetting fields
		if ($this->view == 'programmes')
		{
			$fields = $fields->order_by('order','asc')->get();
			$sections = ProgrammeSection::order_by('order','asc')->get();
			$view = "sortable_index";
		}
		// standard view, so order by field_name not order number
		else
		{
			$fields = $fields->order_by('field_name','asc')->get();
			$view = "index";
		}

		$this->layout->nest('content', 'admin.'.$this->views.'.'.$view , array('fields' => $fields, 'sections' => $sections, 'field_type' => $this->view, 'type'=>$type, 'from' => $this->view));
	}

	public function get_add($type)
	{
		$this->layout->nest('content', 'admin.'.$this->views.'.form', array('field_type'=>$this->view, 'type'=>$type));
	}

	public function get_edit($type, $id)
	{
		$data['id'] = $id;

		$model = $this->model;
		$data['values'] =  $model::find($id);
		$data['field_type'] = $this->view;
		$data['type'] = $type;

		$this->layout->nest('content', 'admin.fields.form', $data);
	}

	public function post_add($type)
	{
		$model = $this->model;
		$name = $this->name;

		if (! $model::is_valid())
		{
			Messages::add('error', $model::$validation->errors->all());
			return Redirect::to($this->views . '/' . $this->view .'/add')->with_input();
		}

		// Add Row
		$field = new $model;

		$field->get_input();

		// By default this is both active and visable.
		$field->active = 1;
		$field->view = 1;

		if(empty($field->programme_field_type)){
			//check that this a kind of programme field before proceeding
			if(strcmp($model, 'ProgrammeField') == 0){
				//set the right value for the kind of field
				if(strcmp($name, 'Programmes') == 0){
					$field->programme_field_type = ProgrammeField::$types['NORMAL'];
				}
				elseif (strcmp($name, 'ProgrammeSettings') == 0) {
					$field->programme_field_type = ProgrammeField::$types['DEFAULT'];
				}
			}
		}

		$field->save();

		// Now we have an ID, set it as the corresponding column name.
		// to avoid risk of duplication
		$colname = Str::slug(Input::get('title'), '_');
		$colname .= '_' . $field->id;
		$field->colname = $colname;
		$field->save();

		//we may want to update several tables, particularly in the case an OVERRIDABLE_DEFAULT field
		$tables_to_update = array($this->table);
		if(strcmp($model, 'ProgrammeField') == 0){
			$tables_to_update = array(Programme::$table, ProgrammeSetting::$table);
		}

		//add relevant table columns
		foreach ($tables_to_update as $table_to_update) {
			$this->update_schema($field->colname, $field->field_initval, $field->field_type, $table_to_update);
		}

		Messages::add('success','Row added to schema');

		return Redirect::to('/'.$type.'/fields/'.$this->view);
	}

	public function post_edit($type)
	{
		$model = $this->model;
		$name = $this->name;

		if (! $model::is_valid(null, array('title'  => 'required|max:255', 'id' => 'required', 'type' => 'in:text,textarea,select,checkbox,help,table_select,table_multiselect')))
		{
			Messages::add('error', $model::$validation->errors->all());
			return Redirect::to('/' . $type . '/' . $this->views . '/' . $this->view .'/edit/' . Input::get('id'))->with_input();
		}

		$field = $model::find(Input::get('id'));

		// Grab the old type it used to be before getting input.
		$oldtype = $field->field_type;

		$field->get_input();

		if(empty($field->programme_field_type)){
			//check that this a kind of programme field before proceeding
			if(strcmp($model, 'ProgrammeField') == 0){
				//set the right value for the kind of field
				if(strcmp($name, 'Programmes') == 0){
					$field->programme_field_type = ProgrammeField::$types['NORMAL'];
				}
				elseif (strcmp($name, 'ProgrammeSettings') == 0) {
					$field->programme_field_type = ProgrammeField::$types['DEFAULT'];
				}
			}
		}
	  
		$field->save();

		// If type changes, apply data type swapper.
		if ($oldtype != Input::get('type')) 
		{
			$type_str = 'varchar(255)';
			if($field->field_type=='textarea') $type_str = 'TEXT';
			
			DB::statement("alter table {$this->table} MODIFY {$field->colname} {$type_str}  DEFAULT '{$field->field_initval}';");
			DB::statement("alter table {$this->table}_revisions MODIFY {$field->colname} {$type_str}  DEFAULT '{$field->field_initval}';");
		}

		Messages::add('success','Edited field.');

		return Redirect::to('/'.$type.'/fields/'.$this->view);
	}

	// This needs to be moved to the model.
	private function update_schema($colname, $init_val, $type, $table_to_update = null)
	{
		if(!$table_to_update){
			$table_to_update = $this->table;
		}

		// Adjust Tables
		Schema::table($table_to_update, function($table) use ($colname, $init_val, $type)
		{
			if ($type=='textarea') {
				$table->text($colname);
			} else {
				$table->string($colname,255)->default($init_val);
			}

		});

		Schema::table($table_to_update.'_revisions', function($table) use ($colname, $init_val, $type)
		{
			if ($type=='textarea')
			{
				$table->text($colname);
			} 
			else 
			{
				$table->string($colname,255)->default($init_val);
			}
		});
	}

	public function get_deactivate($type)
	{
		$model = $this->model;
		$row = $model::find(Input::get('id'));
		$row->active = 0;
		$row->save();

		return Redirect::to($type.'/fields/'.$this->view);
	}

	public function get_reactivate($type)
	{
		$model = $this->model;
		$row = $model::find(Input::get('id'));
		$row->active = 1;
		$row->save();

		return Redirect::to($type.'/fields/'.$this->view);
	}
	
	/**
	 * Routing for POST /reorder
	 *
	 * This allows fields to be reordered via an AJAX request from the UI
	 */
	public function post_reorder()
	{
		$model = $this->model;

		if($model::reorder(Input::get('order'), Input::get('section'))){
			return 'true';
		}else{
			return 'false';
		}
	}

}