<?php
class Fields_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'fields';

    /**
     * Display the fields index page.
     *
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
        $rules = array(
            'title'  => 'required|max:255'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            Messages::add('error',$validation->errors->all());

            return Redirect::to($this->view.'fields/add')->with_input();
        } else {

            $datatype = Input::get('type');

            if (Input::get('id')) {
                $model = $this->model;
                $field = $model::find(Input::get('id'));
                $field->field_name = Input::get('title');
                $field->field_description = Input::get('description');
                $field->field_meta = Input::get('options');

                $oldtype = $field->field_type;
                $field->field_type = Input::get('type');
                $field->field_initval =  Input::get('initval');
                $field->placeholder =  Input::get('placeholder');
                $field->prefill =  (Input::get('prefill')==1) ? 1 : 0;

                $field->save();

                // If type changes, apply data type swapper.
                if ($oldtype != Input::get('type')) {
                    $type_str = 'varchar(255)';
                    if($field->field_type=='textarea') $type_str = 'TEXT';
                    DB::statement("alter table {$this->table} MODIFY {$field->colname} {$type_str}  DEFAULT '{$field->field_initval}';");
                    DB::statement("alter table {$this->table}_revisions MODIFY {$field->colname} {$type_str}  DEFAULT '{$field->field_initval}';");
                }

            } else {
                $colname = Str::slug(Input::get('title'), '_');
                $init_val = Input::get('initval');

                // Add Row
                $model = $this->model;
                $field = new $model;
                $field->field_name = Input::get('title');
                $field->field_type = Input::get('type');
                $field->field_description = Input::get('description');

                $field->field_meta = Input::get('options');
                $field->placeholder =  Input::get('placeholder');
                $field->prefill = (Input::get('prefill')==1) ? 1 : 0;

                $field->field_initval =  $init_val;

                $field->active = 1;
                $field->view = 1;

                $field->save();

                //Now we have an id, set it as part of the colname
                //to avoid risk of duplication
                $colname .= '_'.$field->id;
                $field->colname = $colname;
                $field->save();

                $this->updateSchema($colname, $init_val, $datatype);

            }

            Messages::add('success','Row added to schema');
            //return $this->redirect('index');//Redirect::to('meta/'.$this->table.'s/index');
            return Redirect::to('fields/'.$this->view);
        }
    }

    private function redirect($action)
    {
        return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/fields/'.$this->view.'s/'.$action);
    }

    private function updateSchema($colname, $init_val, $type)
    {
        // Adjust Tables
        Schema::table($this->table, function($table) use ($colname, $init_val, $type) {
            if ($type=='textarea') {
                $table->text($colname);
            } else {
                $table->string($colname,255)->default($init_val);
            }

        });
        Schema::table($this->table.'_revisions', function($table) use ($colname, $init_val, $type) {
            if ($type=='textarea') {
                $table->text($colname);
            } else {
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
