<?php
class Supersubjects_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'supersubjects';
    protected $model = 'Supersubject';

    public function get_index($year, $type)
    {
    	$this->data[$this->views] = Supersubject::where('year', '=', $year)->order_by('id','asc')->get();
        return View::make('admin.'.$this->views.'.index',$this->data);
    }

    public function get_edit($year, $type, $object_id = false){
    	// Do our checks to make sure things are in place
    	if(!$object_id) return Redirect::to($this->views);
    	$object = Supersubject::find($object_id);
    	if(!$object) return Redirect::to($year.'/'.$type.'/'.$this->views);
    	$this->data['supersubject'] = $object;
        $this->data['subjects'] = Subject::getAsList($year);
        $this->data['programmes'] = Programme::getAsList($year);

        if ($revisions = $object->get_revisions()) {
            $this->data['revisions'] = $revisions;
        }
      
    	return View::make('admin.'.$this->views.'.form',$this->data);
    }

    /**
     * Our user subject create function
     *
     **/
    public function get_create($year, $type){
        $this->data['create'] = true;
        $this->data['subjects'] = Subject::getAsList($year);
        $this->data['programmes'] = Programme::getAsList($year);

        return View::make('admin.'.$this->views.'.form',$this->data);
    }

    public function post_delete($year, $type){
        $rules = array(
            'id'  => 'required|exists:supersubjects',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to delete a user that doesn\'t exist.');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }else{
            $supersubject = Supersubject::find(Input::get('id'));
            $supersubject->delete();
            Messages::add('success','Supersubject Removed');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_create($year, $type){
        $rules = array(
            'title'  => 'required|unique:supersubjects|max:255',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/create')->with_input();
        }else{
            $supersubject = new Supersubject;
            $supersubject->title = Input::get('title');
            $supersubject->year = Input::get('year');
            $supersubject->created_by = $this->data['user']->id;
            $supersubject->subject_ids = implode(',',Input::get('linked_subjects') ? Input::get('linked_subjects') : array());
            $supersubject->programme_ids = implode(',',Input::get('linked_programmes') ? Input::get('linked_programmes') : array());

            $supersubject->save();
 
            Messages::add('success','New Supersubject Added');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_edit($year, $type){
        
        $rules = array(
            'id'  => 'required|exists:supersubjects,id',
            'title'  => 'required|max:255|unique:supersubjects,title,'.Input::get('id'),
        );
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/edit/'.Input::get('id'));
        }else{
            $supersubject = Supersubject::find(Input::get('id'));
   
            $supersubject->title = Input::get('title');
            $supersubject->year = Input::get('year');
            $supersubject->subject_ids = implode(',',Input::get('linked_subjects') ? Input::get('linked_subjects') : array());
            $supersubject->programme_ids = implode(',',Input::get('linked_programmes') ? Input::get('linked_programmes') : array());

            $supersubject->save();

            Messages::add('success','Supersubject updated');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }


    /**
     * Routing for GET /$year/$type/supersubjects/$supersubject_id/promote/$revision_id
     * 
     * @param int $year The year of the supersubject (not used, but to keep routing happy).
     * @param string $type The type, either undegrad/postgrade (not used, but to keep routing happy)
     * @param int $supersubject_id The supersubject ID we are promoting a given revision to be live.
     * @param int $revision_id The revision ID we are promote to the being the live output for the supersubject.
     */
    public function get_promote($year, $type, $supersubject_id = false, $revision_id = false) 
    {   
        // Check to see we have what is required.
        if(!$supersubject_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

        // Get revision specified
        $supersubject = Supersubject::find($supersubject_id);

        if (!$supersubject) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $revision = $supersubject->find_revision($revision_id);

        if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $supersubject->useRevision($revision);

        Messages::add('success', "Promoted revision of $revision->title created at $revision->updated_at to live version.");
        return Redirect::to($year.'/'.$type.'/'.$this->views.'');
    }

    /**
     * Routing for GET /$year/$type/supersubjects/$supersubject_id/difference/$revision_id
     *
     * @param int $year The year of the supersubject (not used, but to keep routing happy).
     * @param string $type The type, either undegrad/postgrade (not used, but to keep routing happy)
     * @param int $supersubject_id The supersubject ID we are promoting a given revision to be live.
     * @param int $revision_id The revision ID we are promote to the being the live output for the supersubject.
     */
    public function get_difference($year, $type, $supersubject_id = false, $revision_id = false)
    {
        if(!$supersubject_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

        // Get revision specified
        $supersubject = Supersubject::find($supersubject_id);

        if (!$supersubject) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $revision = $supersubject->find_revision($revision_id);

        if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $supersubject_attributes = $supersubject->attributes;
        $revision_for_diff = (array) $revision;

        // Ignore these fields which will always change
        foreach (array('id', 'created_by', 'published_by', 'created_at', 'updated_at', 'live') as $ignore){
            unset($revision_for_diff[$ignore]);
            unset($supersubject_attributes[$ignore]);
        }

        $differences = array_diff_assoc($supersubject_attributes, $revision_for_diff);

        $diff = array();
        
        foreach ($differences as $field => $value){
            $diff[$field] = SimpleDiff::htmlDiff($supersubject_attributes[$field], $revision_for_diff[$field]);
        }

        $this->data['diff'] = $diff;
        $this->data['new'] = $revision_for_diff;
        $this->data['old'] = $supersubject_attributes;

        $this->data['revision'] = $revision;
        $this->data['supersubject'] = $supersubject;

        return View::make('admin.'.$this->views.'.difference',$this->data);
    }

    /**
     * Routing for GET /changes
     * 
     * The change request page.
     */
    public function get_changes()
    {
       $this->data['revisions'] = DB::table('supersubjects_revisions')
            ->where('status', '=', 'pending')
            ->get();

        return View::make('admin.changes.index', $this->data);
    }

     public function post_deactivate($year, $type)
    {
        $rules = array(
            'id'  => 'required|exists:supersubjects',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to deactivate a post that doesn\'t exist.');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }else{

            $subject = Supersubject::find(Input::get('id'));
            $subject->deactivate();
            Messages::add('success','Super subject deactivated');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }
    }
    public function post_activate($year, $type)
    {
        $rules = array(
            'id'  => 'required|exists:supersubjects',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to activate a post that doesn\'t exist.');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }else{
            $subject = Supersubject::find(Input::get('id'));
            $subject->activate();
            Messages::add('success','Super Subject Activated');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }
    }



}