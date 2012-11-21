<?php
/**
 * Provides a simple interface to single line admin functions of which we have a number.
 * 
 */
class Simple_Admin_Controller extends Admin_Controller {

    // Stores the shortcut variable for the language file
    var $l = '';

    // Whether to use a custom form here
    var $custom_form = false;

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
        
        if ($this->custom_form)
        {
            $this->layout->nest('content', 'admin.'.$this->views.'.form',$this->data);
        }
        else
        {
            $this->layout->nest('content', 'admin.generic-forms.single-field', $this->data);
        }
    }

    public function get_create()
    {
        $this->data['create'] = true;

        if ($this->custom_form)
        {
            $this->layout->nest('content', 'admin.'.$this->views.'.form',$this->data);
        }
        else
        {
            $this->layout->nest('content', 'admin.generic-forms.single-field', $this->data);
        }
    }

    public function post_delete()
    {
        $rules = array(
            'id'  => 'required|exists:' . $this->views,
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Message::add('error', __($this->l . 'error.delete'));

            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
        else
        {
            $remove = $this->model->find(Input::get('id'));
            $remove->delete();

            Messages::add('success', __($this->l . 'success.delete'));
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
 
            Messages::add('success', __($this->l . 'success.create'));

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

            Messages::add('success', __($this->l . 'success.edit'));

            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

}