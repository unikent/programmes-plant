<?php
class Subjects_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'subjects';

    /**
     * Routing for /$year/$type/subjects
     * 
     * @param int $year The year.
     * @param string $type Undergraduate or postgraduate.
     */
    public function get_index($year, $type)
    {
    	$this->data['subjects'] = Subject::where('year', '=', $year)->get();
    	return View::make('admin.'.$this->views.'.index',$this->data);
    }

    /**
     * Routing for GET /$year/$type/edit/$subject_id
     * 
     * @param int $year The year of the subject
     * @param string $type The type of the subject undergraduate/postgraduate
     * @param int $subject_id The ID of the subject to edit.
     */
    public function get_edit($year, $type, $subject_id = false)
    {
    	// Do our checks to make sure things are in place
    	if(!$subject_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

    	$subject = Subject::find($subject_id);

    	if(!$subject) return Redirect::to($year.'/'.$type.'/'.$this->views);

    	$this->data['subject'] = $subject;

        if ($revisions = $subject->get_revisions()) {
            $this->data['revisions'] = $revisions;
        }

        $this->data['field_meta'] = $this->getSubjectMeta();
        $this->data['subjects'] = Subject::getAsList($year);
        $this->data['leaflets'] = Leaflet::getAsList();
        $this->data['school'] = School::getAsList();

    	return View::make('admin.'.$this->views.'.form',$this->data);
    }

    /**
     * Returns a nicely rendered view of the subject on get.
     * 
     * @param int $subject_id The integer for the subject ID.
     * @return View The view view.
     */
    public function get_view ($year, $type, $subject_id = false)
    {
        if(!$subject_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $subject = Subject::find($subject_id);

        if(!$subject) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $this->data['subject'] = $subject;

        return View::make('admin.'. $this->views.'.view', $this->data);
    }

    /**
     * Routing for POST /$year/$type/delete
     * 
     * @param int $year The year.
     * @param string $type ug or pg.
     */
    public function post_deactivate($year, $type)
    {
        $rules = array(
            'id'  => 'required|exists:subjects',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to deactivate a post that doesn\'t exist.');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }else{

            $subject = Subject::find(Input::get('id'));
            $subject->deactivate();
            Messages::add('success','Subject deactivated');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }
    }
    public function post_activate($year, $type)
    {
        $rules = array(
            'id'  => 'required|exists:subjects',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to activate a post that doesn\'t exist.');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }else{
            $subject = Subject::find(Input::get('id'));
            $subject->activate();
            Messages::add('success','Subject Activated');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }
    }

    /**
     * Routing for POST /$year/$type/create
     * 
     * The change request page.
     * 
     * @param int $year The year of the created subject.
     * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
     */
    public function post_create($year, $type)
    {

        $rules = array(
            'title'  => 'required|unique:subjects|max:255',
            'summary' => 'required'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            Messages::add('error',$validation->errors->all());
            return Redirect::to($year.'/'.$type.'/'.$this->views.'/create')->with_input();
        }
        else {


            $subject = new Subject;
            $subject->title = Input::get('title');
            $subject->slug = Str::slug(Input::get('slug'), '-');
            $subject->year = Input::get('year');

            $subject->summary = Input::get('summary');

            $subject->main_school_id = Input::get('school_id');
            $subject->secondary_school_ids = (Input::get('sec_school')!='') ? implode(',',Input::get('sec_school'))  : '';
            $subject->related_subject_ids = (Input::get('rel_subjects')!='') ?  implode(',',Input::get('rel_subjects'))  : '';

            $subject->leaflet_ids = (Input::get('leaflet_ids')!='') ? implode(',',Input::get('leaflet_ids')) : '';

            $subject->created_by = $this->data['user']->id;
            $subject->save();
            
            Messages::add('success','Subject added');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }
    }

    /**
     * Routing for POST /$year/$type/edit
     * 
     * Make a change.
     * 
     * @param int $year The year of the created subject.
     * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
     */
    public function post_edit($year, $type)
    {
        $rules = array(
            'title'  => 'required|max:255',
            'summary' => 'required'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit/')->with_input();
        }
        else
        {
            $subject = Subject::find(Input::get('subject_id'));

            $subject->title = Input::get('title');
            $subject->slug = Str::slug(Input::get('slug'), '-');
            $subject->year = Input::get('year');

            $subject->summary = Input::get('summary');

            $subject->main_school_id = Input::get('school_id');
            $subject->secondary_school_ids = (Input::get('sec_school')!='') ? implode(',',Input::get('sec_school'))  : '';
            $subject->related_subject_ids = (Input::get('rel_subjects')!='') ?  implode(',',Input::get('rel_subjects'))  : '';

            $subject->leaflet_ids = (Input::get('leaflet_ids')!='') ? implode(',',Input::get('leaflet_ids')) : '';

            //Save varible fields
            $f = $this->getSubjectMeta();

            foreach($f as $c){
                $col = $c->colname;
                if(Input::get($col) != null)  $subject->$col = Input::get($col);
            }


            $subject->save();

            Messages::add('success', "Saved $subject->title.");
            return Redirect::to($year.'/'. $type.'/'. 'subjects');
        }
    }


    private function getSubjectMeta(){
        return SubjectMeta::where('active','=','1')->order_by('id','asc')->get();
    }




    /**
     * Our subject create function
     *
     */
    public function get_create($year, $type)
    {

        $this->data['field_meta'] = $this->getSubjectMeta();//SubjectMeta::order_by('id','asc')->get();
        $this->data['school'] = School::getAsList();
        $this->data['subjects'] = Subject::getAsList($year);
        $this->data['leaflets'] = Leaflet::getAsList();

        $this->data['create'] = true;
        return View::make('admin.'.$this->views.'.form',$this->data);
    }

    /**
     * Routing for GET /$year/$type/subjects/$subject_id/promote/$revision_id
     * 
     * @param int $year The year of the subject (not used, but to keep routing happy).
     * @param string $type The type, either undegrad/postgrade (not used, but to keep routing happy)
     * @param int $subject_id The subject ID we are promoting a given revision to be live.
     * @param int $revision_id The revision ID we are promote to the being the live output for the subject.
     */
    public function get_promote($year, $type, $subject_id = false, $revision_id = false) 
    {   
        // Check to see we have what is required.
        if(!$subject_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

        // Get revision specified
        $subject = Subject::find($subject_id);

        if (!$subject) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $revision = $subject->find_revision($revision_id);

        if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $subject->useRevision($revision);

        Messages::add('success', "Promoted revision of $revision->title created at $revision->updated_at to live version.");
        return Redirect::to($year.'/'.$type.'/'.$this->views.'');
    }

    /**
     * Routing for GET /$year/$type/subjects/$subject_id/difference/$revision_id
     *
     * @param int $year The year of the subject (not used, but to keep routing happy).
     * @param string $type The type, either undegrad/postgrade (not used, but to keep routing happy)
     * @param int $subject_id The subject ID we are promoting a given revision to be live.
     * @param int $revision_id The revision ID we are promote to the being the live output for the subject.
     */
    public function get_difference($year, $type, $subject_id = false, $revision_id = false)
    {
        if(!$subject_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

        // Get revision specified
        $subject = Subject::find($subject_id);

        if (!$subject) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $revision = $subject->find_revision($revision_id);

        if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $subject_attributes = $subject->attributes;
        $revision_for_diff = (array) $revision;

        // Ignore these fields which will always change
        foreach (array('id', 'created_by', 'published_by', 'created_at', 'updated_at', 'live') as $ignore){
            unset($revision_for_diff[$ignore]);
            unset($subject_attributes[$ignore]);
        }

        $schools = School::getAsList();
        $sub = Subject::getAsList();

        $subject_attributes['secondary_school_ids'] = $this->splitToText($subject_attributes['secondary_school_ids'],$schools);
        $revision_for_diff['secondary_school_ids'] = $this->splitToText($revision_for_diff['secondary_school_ids'],$schools);
        $subject_attributes['related_subject_ids'] = $this->splitToText($subject_attributes['related_subject_ids'],$sub);
        $revision_for_diff['related_subject_ids'] = $this->splitToText($revision_for_diff['related_subject_ids'],$sub);

        $differences = array_diff_assoc($subject_attributes, $revision_for_diff);


        
        


        $diff = array();

        
        foreach ($differences as $field => $value){
            $diff[$field] = SimpleDiff::htmlDiff($subject_attributes[$field], $revision_for_diff[$field]);
        }

        $this->data['diff'] = $diff;
        $this->data['new'] = $revision_for_diff;
        $this->data['old'] = $subject_attributes;
        $this->data['attributes'] = Subject::getAttributesList();


        $this->data['revision'] = $revision;
        $this->data['subject'] = $subject;

        return View::make('admin.'.$this->views.'.difference',$this->data);
    }

    /**
     * Routing for GET /changes
     * 
     * The change request page.
     */
    public function get_changes()
    {
       $this->data['revisions'] = DB::table('subjects_revisions')
            ->where('status', '=', 'pending')
            ->get();

        return View::make('admin.changes.index', $this->data);
    }

    private function splitToText($list,$options){
        if($list == '' || $list == null) return '';

        $list = explode(',',$list);
        $l_str = '';
        foreach($list as $val){
            $l_str .= $options[$val].', ';

        }
        return $l_str;


    }
}