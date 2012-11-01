<?php
class Programmes_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'programmes';
    protected $model = 'Programme';

    /**
     * Routing for /$year/$type/subjects
     * 
     * @param int $year The year.
     * @param string $type Undergraduate or postgraduate.
     */
    public function get_index($year, $type)
    {
        $model = $this->model;
        $this->data[$this->views] = $model::where('year', '=', $year)->get();
        $this->data['subjectList'] = Subject::getAsList();
        return View::make('admin.'.$this->views.'.index',$this->data);
    }


    /**
     * Present a form to allow ther creation of a new subject.
     * If an item_id is passed, present the form prefilled with the item's values
     * 
     * @param int $year The year
     * @param string $type Undergraduate or postgraduate.
     * @param int $item_id The ID of the programme to clone from.
     */
    public function get_create($year, $type, $item_id = false)
    {
        if($item_id){
            // We're cloning item_id
            $model = $this->model;
            $course =  $model::find($item_id);
            $this->data['clone'] = true;
            $this->data[$this->views] = $course ;
        }
        else {
            $this->data['clone'] = false;
        }

        $this->data['field_meta'] = $this->getSubjectMeta();//SubjectMeta::order_by('id','asc')->get();
        $this->data['subjects'] = Subject::getAsList($year);
        $this->data['campuses'] = Campus::getAsList();
        $this->data['school'] = School::getAsList();
        $this->data['awards'] = Award::getAsList();
        $this->data['programme_list'] = Programme::getAsList($year);
        $this->data['leaflets'] = Leaflet::getAsList();


        //print_r( $this->data['subjects']);
        $this->data['create'] = true;
        return View::make('admin.'.$this->views.'.form',$this->data);
    }
    
    /**
     * Routing for GET /$year/$type/edit/$subject_id
     * 
     * @param int $year The year
     * @param string $type Undergraduate or postgraduate.
     * @param int $item_id The ID of the programme to edit.
     */
    public function get_edit($year, $type, $itm_id = false)
    {
    	// Do our checks to make sure things are in place
    	if(!$itm_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

        // Ensure we have a corresponding course in the database
        $model = $this->model;
    	$course =  $model::find($itm_id);
        if(!$course) return Redirect::to($year.'/'.$type.'/'.$this->views);

    	$this->data[$this->views] = $course ;

        if ($revisions = $course->get_revisions()) {
            $this->data['revisions'] =  $revisions;
        }

        $this->data['field_meta'] = $this->getSubjectMeta(); //SubjectMeta::order_by('id','asc')->get();

        $this->data['subjects'] = Subject::getAsList($year);
        $this->data['programme_list'] = Programme::getAsList($year);
        $this->data['leaflets'] = Leaflet::getAsList();

        $this->data['campuses'] = Campus::getAsList();
        $this->data['school'] = School::getAsList();
        $this->data['awards'] = Award::getAsList();

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
    public function post_delete($year, $type)
    {
        $rules = array(
            'id'  => 'required|exists:programmes',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to delete a post that doesn\'t exist.');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }else{
            $subject = Subject::find(Input::get('id'));
            $subject->delete();
            Messages::add('success','Subject Removed');
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
            'title'  => 'required|unique:programmes|max:255',
            'summary' => 'required',
            'subject_id' => 'required'
          //  'year' => 'required'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            Messages::add('error',$validation->errors->all());
            return Redirect::to($year.'/'.$type.'/'.$this->views.'/create')->with_input();
        }
        else {
            $programme = new Programme;
            $programme->title = Input::get('title');
            $programme->slug = Str::slug(Input::get('slug'), '-');
            $programme->year = Input::get('year');

            $programme->summary = Input::get('summary');
            $programme->created_by = $this->data['user']->id;
            $programme->honours = Input::get('award');

            $programme->school_id = Input::get('school_id');
            $programme->school_adm_id = Input::get('school_adm_id');
            $programme->campus_id = Input::get('campus_id');
            $programme->subject_id = Input::get('subject_id');

            $programme->leaflet_ids = (Input::get('leaflet_ids')!='') ? implode(',',Input::get('leaflet_ids')) : '';

            $programme->related_school_ids = (Input::get('rel_schools')!='') ? implode(',',Input::get('rel_schools')) : '';
            $programme->related_subject_ids = (Input::get('rel_subjects')!='') ? implode(',',Input::get('rel_subjects')) : '';
            $programme->related_programme_ids = (Input::get('rel_programmes')!='') ? implode(',',Input::get('rel_programmes')) : '';

            $programme->mod_1_title = Input::get('mod_1_title');
            $programme->mod_1_content = Input::get('mod_1_content');
            $programme->mod_2_title = Input::get('mod_2_title');
            $programme->mod_2_content = Input::get('mod_2_content');
            $programme->mod_3_title = Input::get('mod_3_title');
            $programme->mod_3_content = Input::get('mod_3_content');
            $programme->mod_4_title = Input::get('mod_4_title');
            $programme->mod_4_content = Input::get('mod_4_content');
            $programme->mod_5_title = Input::get('mod_5_title');
            $programme->mod_5_content = Input::get('mod_5_content');

             //Save varible fields
            $f = $this->getSubjectMeta();//SubjectMeta::order_by('id','asc')->get();
            foreach($f as $c){
                $col = $c->colname;
                if(Input::get($col) != null)  $programme->$col = Input::get($col);
            }

            $programme->save();
            
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
            'summary' => 'required',
            'subject_id' => 'required'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit/')->with_input();
        }
        else
        {
            $programme = Programme::find(Input::get('programme_id'));

            $programme->title = Input::get('title');
            $programme->slug = Str::slug(Input::get('slug'), '-');
            $programme->year = Input::get('year');

            $programme->summary = Input::get('summary');
            $programme->honours = Input::get('award');

            $programme->school_id = Input::get('school_id');
            $programme->school_adm_id = Input::get('school_adm_id');
            $programme->campus_id = Input::get('campus_id');
            $programme->subject_id = Input::get('subject_id');

            $programme->leaflet_ids = (Input::get('leaflet_ids')!='') ? implode(',',Input::get('leaflet_ids')) : '';

            $programme->related_school_ids = (Input::get('rel_schools')!='') ? implode(',',Input::get('rel_schools')) : '';
            $programme->related_subject_ids = (Input::get('rel_subjects')!='') ? implode(',',Input::get('rel_subjects')) : '';
            $programme->related_programme_ids = (Input::get('rel_programmes')!='') ? implode(',',Input::get('rel_programmes')) : '';

            $programme->mod_1_title = Input::get('mod_1_title');
            $programme->mod_1_content = Input::get('mod_1_content');
            $programme->mod_2_title = Input::get('mod_2_title');
            $programme->mod_2_content = Input::get('mod_2_content');
            $programme->mod_3_title = Input::get('mod_3_title');
            $programme->mod_3_content = Input::get('mod_3_content');
            $programme->mod_4_title = Input::get('mod_4_title');
            $programme->mod_4_content = Input::get('mod_4_content');
            $programme->mod_5_title = Input::get('mod_5_title');
            $programme->mod_5_content = Input::get('mod_5_content');

            //Save varible fields
            $f = $this->getSubjectMeta();//SubjectMeta::order_by('id','asc')->get();
            foreach($f as $c){
                $col = $c->colname;
                if(Input::get($col) != null)  $programme->$col = Input::get($col);
            }


            $programme->save();

            Messages::add('success', "Saved $programme->title.");
            return Redirect::to($year.'/'. $type.'/'. $this->views);
        }
    }


    private function getSubjectMeta(){
        $model = $this->model.'Meta';
        return  $model::where('active','=','1')->order_by('id','asc')->get();
    }





    /**
     * Routing for GET /$year/$type/subjects/$subject_id/promote/$revision_id
     * 
     * @param int $year The year of the subject (not used, but to keep routing happy).
     * @param string $type The type, either undegrad/postgrade (not used, but to keep routing happy)
     * @param int $subject_id The subject ID we are promoting a given revision to be live.
     * @param int $revision_id The revision ID we are promote to the being the live output for the subject.
     */
    public function get_promote($year, $type, $programme_id = false, $revision_id = false) 
    {   
        // Check to see we have what is required.
        if(!$programme_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

        // Get revision specified
        $programme = Programme::find($programme_id);

        if (!$programme) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $revision = $programme->find_revision($revision_id);

        if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);


        $programme->useRevision($revision);

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
        $subject = Programme::find($subject_id);

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
        $pro = Programme::getAsList();

        $subject_attributes['related_school_ids'] = $this->splitToText($subject_attributes['related_school_ids'],$schools);
        $revision_for_diff['related_school_ids'] = $this->splitToText($revision_for_diff['related_school_ids'],$schools);
        $subject_attributes['related_subject_ids'] = $this->splitToText($subject_attributes['related_subject_ids'],$sub);
        $revision_for_diff['related_subject_ids'] = $this->splitToText($revision_for_diff['related_subject_ids'],$sub);
        $subject_attributes['related_programme_ids'] = $this->splitToText($subject_attributes['related_programme_ids'],$pro);
        $revision_for_diff['related_programme_ids'] = $this->splitToText($revision_for_diff['related_programme_ids'],$pro);

        $differences = array_diff_assoc($subject_attributes, $revision_for_diff);

        $diff = array();
        
        foreach ($differences as $field => $value){
            $diff[$field] = SimpleDiff::htmlDiff($subject_attributes[$field], $revision_for_diff[$field]);
        }

        $this->data['diff'] = $diff;
        $this->data['new'] = $revision_for_diff;
        $this->data['old'] = $subject_attributes;

        $this->data['attributes'] = Programme::getAttributesList();



        $this->data['revision'] = $revision;
        $this->data['subject'] = $subject;

        return View::make('admin.'.$this->views.'.difference',$this->data);
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

     public function post_deactivate($year, $type)
    {
        $rules = array(
            'id'  => 'required|exists:programmes',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to deactivate a post that doesn\'t exist.');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }else{

            $subject = Programme::find(Input::get('id'));
            $subject->deactivate();
            Messages::add('success','Programme deactivated');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }
    }
    public function post_activate($year, $type)
    {
        $rules = array(
            'id'  => 'required|exists:programmes',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to activate a post that doesn\'t exist.');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }else{
            $subject = Programme::find(Input::get('id'));
            $subject->activate();
            Messages::add('success','Programme Activated');
            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }
    }
}