<?php
/**
 * Provides a simple interface to single line admin functions of which we have a number.
 * 
 */
class Simple_Admin_Controller extends Admin_Controller {

    // Stores the shortcut variable for the language file
    var $l = '';

    public function __construct()
    {  
    	if ($this->model) {
    		$this->model = new $this->model;
    	}

        // Quick use variable for access to language files
        $this->l = $this->views . '.';

    	// Construct parent.
    	parent::__construct();
    }

    public function get_index()
    {
    	$this->data['items'] = $this->model->order_by('id','asc')->get();

        $this->layout->nest('content', 'admin.indexes.simple-index', $this->data);
    }
    
    public function get_edit($year, $type, $object_id = false)
    {
    	// Do our checks to make sure things are in place
    	if(!$object_id) return Redirect::to($this->views);

    	$object = $this->model->find($object_id);

    	if(!$object) return Redirect::to($this->views);

    	$this->data['item'] = $object;
      
    	$this->layout->nest('content', 'admin.forms.single-field', $this->data);
    }

    public function get_create()
    {
        $this->data['create'] = true;

        $this->layout->nest('content', 'admin.forms.single-field', $this->data);
    }

    public function post_delete()
    {
        $rules = array(
            'id'  => 'required|exists:' . $this->views,
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error','You tried to delete a' . Str::singular($this->views) . 'that doesn\'t exist.');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
        else
        {
            $remove = $this->model->find(Input::get('id'));
            $remove->delete();

            Messages::add('success', Str::title(Str::singular($this->views)) . ' Removed');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_create()
    {
        $rules = array(
            'name'  => 'required|unique:' . $this->views . '|max:255',
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/create')->with_input();
        }
        else
        {
            $new = new $this->model;
            $new->name = Input::get('name');

            $new->save();
 
            Messages::add('success','New ' . Str::title($this->views) . ' Added');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_edit()
    {
        $rules = array(
            'id'  => 'required|exists:'. $this->views .',id',
            'name'  => 'required|max:255|unique:'. $this->views . ',name,'.Input::get('id'),
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/edit/'.Input::get('id'));
        }
        else
        {
            $update = $this->model->find(Input::get('id'));
   
            $update->name = Input::get('name');

            $update->save();

            Messages::add('success', $this->views . ' updated');

            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

}