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
        $fields = $model::select('*');
        
        if($this->where_clause){
            foreach ($this->where_clause as $clause) {
                $fields = $fields->or_where($clause[0], $clause[1], $clause[2]);
            }
        }

        $fields = $fields->order_by('order','asc')->get();
        
        // sections
        $sections = "";
        // only show sections on the programme fields lising page ie we don't want them for globalsetting fields or programmesetting fields
        if ($this->view == 'programmes')
        {
            $sections = ProgrammeSection::order_by('order','asc')->get();
        }

        return View::make('admin.'.$this->views.'.index', array('fields' => $fields, 'sections' => $sections, 'field_type' => $this->view));
    }

    public function get_add()
    {
        return View::make('admin.'.$this->views.'.form',array('field_type'=>$this->view));
    }

    public function get_edit($id)
    {

        $data['id'] = $id;

        $model = $this->model;
        $data['values'] =  $model::find($id);
        $data['field_type'] = $this->view;

        return View::make('admin.fields.form',$data);
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
                $subject = $model::find(Input::get('id'));
                $subject->field_name = Input::get('title');
                $subject->field_description = Input::get('description');
                $subject->field_meta = Input::get('options');

                $oldtype = $subject->field_type;
                $subject->field_type = Input::get('type');
                $subject->field_initval =  Input::get('initval');
                $subject->placeholder =  Input::get('placeholder');
                $subject->prefill =  (Input::get('prefill')==1) ? 1 : 0;

                if($this->where_clause){
                    $where_field = $this->where_clause[0];
                    $subject->$where_field = $this->where_clause[2];
                }

                $subject->save();

                //If type changes, apply data type swapper.
                if ($oldtype != Input::get('type')) {
                    $type_str = 'varchar(255)';
                    if($subject->field_type=='textarea') $type_str = 'TEXT';
                    DB::statement("alter table {$this->table} MODIFY {$subject->colname} {$type_str}  DEFAULT '{$subject->field_initval}';");
                    DB::statement("alter table {$this->table}_revisions MODIFY {$subject->colname} {$type_str}  DEFAULT '{$subject->field_initval}';");
                }

            } else {
                $colname = Str::slug(Input::get('title'), '_');
                $init_val = Input::get('initval');

                //Add Row
                $model = $this->model;
                $subject = new $model;
                $subject->field_name = Input::get('title');
                $subject->field_type = Input::get('type');
                $subject->field_description = Input::get('description');

                $subject->field_meta = Input::get('options');
                $subject->placeholder =  Input::get('placeholder');
                $subject->prefill = (Input::get('prefill')==1) ? 1 : 0;

                $subject->field_initval =  $init_val;

                $subject->active = 1;
                $subject->view = 1;

                $subject->save();

                //Now we have an id, set it as part of the colname
                //to avoid risk of duplication
                $colname .= '_'.$subject->id;
                $subject->colname = $colname;
                $subject->save();

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
        //Adjust Tables
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
    
    /**
     * Routing for POST /reorder
     *
     * This allows fields to be reordered via an AJAX request from the UI
     */
    public function post_reorder()
    {
        $model = $this->model;
        $model::reorder(Input::get('order'));
    }
}
