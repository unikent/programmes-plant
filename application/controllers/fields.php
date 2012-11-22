<?php
class Fields_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'fields';

    /**
     * Display the fields index page.
     */
    public function get_index()
    {
        $model = $this->model;
        $fields = $model::order_by('id','asc')->get();

        $this->layout->nest('content', 'admin.'.$this->views.'.index', array('fields' => $fields, 'field_type' => $this->view));
    }

    public function get_add()
    {
        $this->layout->nest('content', 'admin.'.$this->views.'.form', array('field_type'=>$this->view));
    }

    public function get_edit($id)
    {
        $data['id'] = $id;

        $model = $this->model;
        $data['values'] =  $model::find($id);
        $data['field_type'] = $this->view;

        $this->layout->nest('content', 'admin.fields.form', $data);
    }

    public function post_add()
    {
        $model = $this->model;

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
        
        $field->save();

        // Now we have an ID, set it as the corresponding column name.
        // to avoid risk of duplication
        $colname = Str::slug(Input::get('title'), '_');
        $colname .= '_' . $field->id;
        $field->colname = $colname;
        $field->save();

        $this->update_schema($field->colname, $field->field_initval, $field->type);

        Messages::add('success','Row added to schema');

        return Redirect::to('fields/'.$this->view);
    }

    public function post_edit()
    {
        $model = $this->model;

        if (! $model::is_valid(null, array('title'  => 'required|max:255', 'id' => 'required', 'type' => 'in:text,textarea,select,checkbox')))
        {
            Messages::add('error', $model->validation->errors->all());
            return Redirect::to($this->views . '/' . $this->view .'/add')->with_input();
        }

        $field = $model::find(Input::get('id'));

        // Grab the old type it used to be before getting input.
        $oldtype = $field->field_type;

        $field->get_input();
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

        return Redirect::to('fields/'.$this->view);
    }

    // This needs to be moved to the model.
    private function update_schema($colname, $init_val, $type)
    {
        // Adjust Tables
        Schema::table($this->table, function($table) use ($colname, $init_val, $type)
        {
            if ($type=='textarea') {
                $table->text($colname);
            } else {
                $table->string($colname,255)->default($init_val);
            }

        });

        Schema::table($this->table.'_revisions', function($table) use ($colname, $init_val, $type)
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

    public function get_deactivate()
    {
        $model = $this->model;
        $row = $model::find(Input::get('id'));
        $row->active = 0;
        $row->save();

        return Redirect::to('fields/'.$this->view);
    }

    public function get_reactivate()
    {
        $model = $this->model;
        $row = $model::find(Input::get('id'));
        $row->active = 1;
        $row->save();

        return Redirect::to('fields/'.$this->view);
    }
}
