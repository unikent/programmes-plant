<?php

class GlobalSettings_Controller extends Admin_Controller
{
    public $restful = true;
    public $views = 'globalsettings';
    protected $model = 'GlobalSetting';

    /**
     * Routing for /$year/$type/globalsettings
     *
     * @param int    $year The year.
     * @param string $type Undergraduate or postgraduate.
     */
    public function get_index($year, $type)
    {

        $model = $this->model;
        $data = $model::where('year', '=', $year)->first();
        if ($data == null) {
            return Redirect::to($year.'/'.$type.'/'.$this->views.'/create');
        } else {
            return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit');
        }

    }

    /**
     * Our subject create function
     *
     */
    public function get_create($year, $type)
    {

        $this->data['fields'] = $this->get_fields();

        //print_r( $this->data['subjects']);
        $this->data['create'] = true;

        return View::make('admin.'.$this->views.'.form',$this->data);
    }

    /**
     * Routing for GET /$year/$type/edit/$subject_id
     *
     * @param int    $year       The year of the subject
     * @param string $type       The type of the subject undergraduate/postgraduate
     * @param int    $subject_id The ID of the subject to edit.
     */
    public function get_edit($year, $type)
    {

        // Do our checks to make sure things are in place
        //if(!$itm_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $model = $this->model;
        $globalsetting = $model::where('year', '=', $year)->first();

        if(!$globalsetting) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $this->data[$this->views] = $globalsetting ;

        if ($revisions = $globalsetting->get_revisions()) {
            $this->data['revisions'] =  $revisions;
        }

        $this->data['fields'] = $this->get_fields();

        return View::make('admin.'.$this->views.'.form',$this->data);
    }

    /**
     * Routing for POST /$year/$type/create
     *
     * The change request page.
     *
     * @param int    $year The year of the created subject.
     * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
     */
    public function post_create($year, $type)
    {

            $subject = new GlobalSetting;
            $subject->year = Input::get('year');
            $subject->institution = Input::get('institution');

             //Save varible fields
            $f = $this->get_fields();
            foreach ($f as $c) {
                $col = $c->colname;
                if(Input::get($col) != null)  $subject->$col = Input::get($col);
            }

            $subject->save();

            Messages::add('success','Subject added');

            return Redirect::to($year.'/'.$type.'/'.$this->views.'');

    }

    /**
     * Routing for POST /$year/$type/edit
     *
     * Make a change.
     *
     * @param int    $year The year of the created subject.
     * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
     */
    public function post_edit($year, $type)
    {

            $subject = GlobalSetting::where('year', '=', $year)->first();

            $subject->year = Input::get('year');
            $subject->institution = Input::get('institution');

            //Save varible fields
            $f = $this->get_fields();
            foreach ($f as $c) {
                $col = $c->colname;
                if(Input::get($col) != null)  $subject->$col = Input::get($col);
            }

            $subject->save();

            Messages::add('success', "Saved $subject->institution.");

            return Redirect::to($year.'/'. $type.'/'. $this->views);

    }

    private function get_fields()
    {
        $model = 'GlobalSettingField';

        return  $model::where('active','=','1')->order_by('id','asc')->get();
    }

    /**
     * Routing for GET /$year/$type/subjects/$subject_id/promote/$revision_id
     *
     * @param int    $year        The year of the subject (not used, but to keep routing happy).
     * @param string $type        The type, either undegrad/postgrade (not used, but to keep routing happy)
     * @param int    $subject_id  The subject ID we are promoting a given revision to be live.
     * @param int    $revision_id The revision ID we are promote to the being the live output for the subject.
     */
    public function get_promote($year, $type, $revision_id = false)
    {
        // Check to see we have what is required.
        //if(!$programme_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

        // Get revision specified
        $globalsetting = GlobalSetting::where('year', '=', $year)->first();

        if (!$globalsetting) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $revision = $globalsetting->find_revision($revision_id);

        if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $globalsetting->useRevision($revision);

        /*
        // Copy revision into the live table
        $subject->title = $revision->title;
        $subject->slug = $revision->slug;
        $subject->factbox = $revision->factbox;
        $subject->summary = $revision->summary;
        $subject->year = $revision->year;
        $subject->created_by = $revision->created_by;
        $subject->created_at = $revision->created_at;
        $subject->updated_at = $revision->updated_at;

        // Add details of who promoted this copy to live.
        $subject->published_by = $this->data['user']->id;

        // Flag this revision as the current revision.
        $revision->status = 'live';

        // Save.
        $subject->save();
    */
        Messages::add('success', "Promoted revision created at $revision->updated_at to live version.");

        return Redirect::to($year.'/'.$type.'/'.$this->views.'');
    }

    /**
     * Routing for GET /$year/$type/subjects/$subject_id/difference/$revision_id
     *
     * @param int    $year        The year of the subject (not used, but to keep routing happy).
     * @param string $type        The type, either undegrad/postgrade (not used, but to keep routing happy)
     * @param int    $subject_id  The subject ID we are promoting a given revision to be live.
     * @param int    $revision_id The revision ID we are promote to the being the live output for the subject.
     */
    public function get_difference($year, $type, $revision_id = false)
    {

        // Get revision specified
        $globalsetting = GlobalSetting::where('year', '=', $year)->first();

        if (!$globalsetting) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $revision = $globalsetting->find_revision($revision_id);
        
        if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $revision_attributes = $globalsetting->attributes;
        $revision_for_diff = (array) $revision;

        // Ignore these fields which will always change
        foreach (array('id', 'created_by', 'published_by', 'created_at', 'updated_at', 'live') as $ignore) {
            unset($revision_for_diff[$ignore]);
            unset($revision_attributes[$ignore]);
        }

        $differences = array_diff_assoc($revision_attributes, $revision_for_diff);

        $diff = array();

        foreach ($differences as $field => $value) {
            $diff[$field] = SimpleDiff::htmlDiff($revision_attributes[$field], $revision_for_diff[$field]);
        }

        $this->data['diff'] = $diff;
        $this->data['new'] = $revision_for_diff;
        $this->data['old'] = $revision_attributes;
        $this->data['attributes'] = GlobalSetting::getAttributesList();

        $this->data['revision'] = $revision;
        $this->data['globalsetting'] = $globalsetting;

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
}
