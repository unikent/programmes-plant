<?php
class Meta_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'meta';


    /**
     * Display Edit meta index page.
     * 
     */
    public function get_index()
    {
        $model = $this->table.'Meta';
        $fields = $model::order_by('id','asc')->get();
       
        return View::make('admin.'.$this->views.'.index', array('fields' => $fields, 'meta_type' => $this->table));
    }

    public function get_add()
    {
        return View::make('admin.'.$this->views.'.form',array('meta_type'=>$this->table));
    }

    public function get_edit($year,$type,$id)
    {

        $data['id'] = $id;

        $model = $this->table.'Meta';
        $data['values'] =  $model::find($id);
        $data['meta_type'] = $this->table;

        return View::make('admin.'.$this->views.'.form',$data);
    }


    public function post_add()
    {
        $rules = array(
            'title'  => 'required|max:255'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            Messages::add('error',$validation->errors->all());
            return Redirect::to($this->table.'s_meta/add')->with_input();
        }
        else {

            $datatype = Input::get('type');


            if(Input::get('id')){
                $model = $this->table.'Meta';
                $subject = $model::find(Input::get('id'));
                $subject->field_name = Input::get('title');
                $subject->field_description = Input::get('description');
                $subject->field_meta = Input::get('options');
                
                $oldtype = $subject->field_type;
                $subject->field_type = Input::get('type');
                $subject->field_initval =  Input::get('initval');
                $subject->placeholder =  Input::get('placeholder');
                $subject->prefill =  (Input::get('prefill')==1) ? 1 : 0;

                $subject->save();

                //If type changes, apply data type swapper.
                if($oldtype != Input::get('type')){
                    $type_str = 'varchar(255)';
                    if($subject->field_type=='textarea') $type_str = 'TEXT';
                    DB::statement("alter table {$this->table}s MODIFY {$subject->colname} {$type_str}  DEFAULT '{$subject->field_initval}';");
                    DB::statement("alter table {$this->table}s_revisions MODIFY {$subject->colname} {$type_str}  DEFAULT '{$subject->field_initval}';");  
                }
               
            }else{
                $colname = Str::slug(Input::get('title'), '_');
                $init_val = Input::get('initval');

                //Add Row
                $model = $this->table.'Meta';
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
            return $this->redirect('index');//Redirect::to('meta/'.$this->table.'s/index');
        }
    }

    private function redirect($action){
        return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/meta/'.$this->table.'s/'.$action);
    }


    private function updateSchema($colname, $init_val, $type){
        //Adjust Tables
        Schema::table($this->table.'s', function($table) use ($colname, $init_val, $type){
            if($type=='textarea'){
                $table->text($colname);
            }else{
                $table->string($colname,255)->default($init_val);
            }
                    
        });
        Schema::table($this->table.'s_revisions', function($table) use ($colname, $init_val, $type){
            if($type=='textarea'){
                $table->text($colname);
            }else{
                $table->string($colname,255)->default($init_val);
            }
        });
    }


    public function get_deactivate()
    {
        $row = SubjectMeta::find(Input::get('id'));
        $row->active = 0;
        $row->save();
        return $this->redirect('index');
    }

     public function get_reactivate()
    {
        $row = SubjectMeta::find(Input::get('id'));
        $row->active = 1;
        $row->save();
        return $this->redirect('index');
    }
}