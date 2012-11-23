<?php
/**
 * Simple_Admin_Controller
 * 
 * Provides a simple interface to single line admin functions.
 * 
 * We have a number of these: awards, schools and so on.
 */
class Simple_Admin_Controller extends Admin_Controller {

    // Stores the shortcut variable for the language file
    var $l = '';

    // Whether to use a custom form here
    var $custom_form = false;

    public function __construct()
    {
        // Quick use variable for access to language files
        $this->l = $this->views . '.';

    	// Construct parent.
    	parent::__construct();
    }

    /**
     * Return all data and send to an index view.
     */
    public function get_index()
    {
        $model = $this->model;

        $this->data['items'] = $model::all();

        $this->layout->nest('content', 'admin.indexes.simple-index', $this->data);
    }
    
    /**
     * Display an edit form for an item.
     */
    public function get_edit($year, $type, $object_id = false)
    {
    	if (! $object_id)
        {
            Message::add('error', "No " . Str::lower($this->model) . "ID provided, so could not be loaded.");
            return Redirect::to($this->views);
        }

        $this->model = $model;

    	$object = $model::find($object_id);

    	if (! $object)
        {
            Message::add('error', __($this->l . 'error.not_found'));
            return Redirect::to($this->views);
        }

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

    /**
     * Show a creation form for an item.
     */
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

    /**
     * Route for deletion of an item.
     */
    public function post_delete()
    {
        $model = $this->model;

        $rules = array(
            'id'  => 'required|exists:' . $this->views,
        );

        if (! $model::is_valid(null, $rules))
        {
            Message::add('error', __($this->l . 'error.delete'));

            return Redirect::to(URI::segment(1) . '/' . URI::segment(2) . '/' . $this->views);
        }
        else
        {
            $remove = $model::find(Input::get('id'));
            $remove->delete();

            Messages::add('success', __($this->l . 'success.delete'));
            return Redirect::to(URI::segment(1) . '/' . URI::segment(2) . '/' . $this->views);
        }
    }

    /**
     * Create a new item via POST.
     */
    public function post_create()
    {
        $model = $this->model;

        $rules = array(
            'name'  => 'required|unique:' . $this->views . '|max:255',
        );

        if (! $model::is_valid(null, $rules))
        {
            Messages::add('error', $model::$validation->errors->all());

            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/create')->with_input();
        }

        $new = new $this->model;

        $new->name = Input::get('name');
        $new->save();

        Messages::add('success', __($this->l . 'success.create'));

        return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
    }

    /**
     * Edit an item via POST.
     */
    public function post_edit()
    {
        $model = $this->model;
        
        $rules = array(
            'id'  => 'required|exists:'. $this->views .',id',
            'name'  => 'required|max:255|unique:'. $this->views . ',name,'.Input::get('id'),
        );

        if (! $model::is_valid())
        {
            Messages::add('error', $model::$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/edit/'.Input::get('id'));
        }

        $update = $model::find(Input::get('id'));

        $update->name = Input::get('name');

        $update->save();

        Messages::add('success', __($this->l . 'success.edit'));

        return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
    }

}