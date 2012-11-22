<?php
class Programmes_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'programmes';
    protected $model = 'Programme';

    /**
     * Routing for /$year/$type/programmes
     *
     * @param int    $year The year.
     * @param string $type Undergraduate or postgraduate.
     */
    public function get_index($year, $type)
    {
        $model = $this->model;
        $this->data[$this->views] = $model::where('year', '=', $year)->get();
        $this->data['programmeList'] = Programme::getAsList();

        return View::make('admin.'.$this->views.'.index',$this->data);
    }

    /**
     * Present a form to allow ther creation of a new programme.
     * If an item_id is passed, present the form prefilled with the item's values
     *
     * @param int    $year    The year
     * @param string $type    Undergraduate or postgraduate.
     * @param int    $item_id The ID of the programme to clone from.
     */
    public function get_create($year, $type, $item_id = false)
    {
        if ($item_id) {
            // We're cloning item_id
            $model = $this->model;
            $course = $model::find($item_id);
            $this->data['clone'] = true;
            $this->data[$this->views] = $course ;
        } else {
            $this->data['clone'] = false;
        }

        $this->data['fields'] = $this->getProgrammeFields();
        //$this->data['fields'] = ProgrammeField::programme_fields_by_section();
        $this->data['campuses'] = Campus::getAsList();
        $this->data['school'] = School::getAsList();
        $this->data['awards'] = Award::getAsList();
        $this->data['programme_list'] = Programme::getAsList($year);
        $this->data['leaflets'] = Leaflet::getAsList();
        $this->data['create'] = true;

        return View::make('admin.'.$this->views.'.form',$this->data);
    }

    /**
     * Routing for GET /$year/$type/edit/$programme_id
     *
     * @param int    $year    The year
     * @param string $type    Undergraduate or postgraduate.
     * @param int    $item_id The ID of the programme to edit.
     */
    public function get_edit($year, $type, $itm_id = false)
    {
        // Do our checks to make sure things are in place
        if(!$itm_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

      // Ensure we have a corresponding course in the database
      $model = $this->model;
      $course = $model::find($itm_id);
      if(!$course) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $this->data[$this->views] = $course ;

        if ($revisions = $course->get_revisions()) {
            $this->data['revisions'] =  $revisions;
        }

        $this->data['fields'] = $this->getProgrammeFields();
        $this->data['title_field'] = Programme::get_title_field();

        return View::make('admin.'.$this->views.'.form',$this->data);
    }

    /**
     * Routing for POST /$year/$type/create
     *
     * The change request page.
     *
     * @param int    $year The year of the created programme.
     * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
     */
    public function post_create($year, $type)
    {
        $rules = array(
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            Messages::add('error',$validation->errors->all());

            return Redirect::to($year.'/'.$type.'/'.$this->views.'/create')->with_input();
        } else {
            $programme = new Programme;
            $programme->year = Input::get('year');

            $programme->created_by = Auth::user();

            $programme->save();
            Messages::add('success','Programme added');

            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }
    }

    /**
     * Routing for POST /$year/$type/edit
     *
     * Make a change.
     *
     * @param int    $year The year of the created programme
     * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
     */
    public function post_edit($year, $type)
    {
        $rules = array(
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            Messages::add('error',$validation->errors->all());

            return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit/')->with_input();
        } else {
            $programme = Programme::find(Input::get('programme_id'));

            $programme->year = Input::get('year');

            $programme->save();

            $title_field = Programme::get_title_field();
            Messages::add('success', "Saved ".$programme->$title_field);

            return Redirect::to($year.'/'. $type.'/'. $this->views);
        }
    }

    private function getProgrammeFields()
    {
        $model = $this->model.'Field';

        return  $model::where('active','=','1')->where_in('programme_field_type', array(ProgrammeField::$types['OVERRIDABLE_DEFAULT'], ProgrammeField::$types['NORMAL']))->order_by('order','asc')->get();
    }

    /**
     * Routing for GET /$year/$type/programmes/$programme_id/promote/$revision_id
     *
     * @param int    $year         The year of the programme (not used, but to keep routing happy).
     * @param string $type         The type, either undegrad/postgrade (not used, but to keep routing happy)
     * @param int    $programme_id The programme ID we are promoting a given revision to be live.
     * @param int    $revision_id  The revision ID we are promote to the being the live output for the programme.
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
     * Routing for GET /$year/$type/programmes/$programme_id/difference/$revision_id
     *
     * @param int    $year         The year of the programme (not used, but to keep routing happy).
     * @param string $type         The type, either undegrad/postgrade (not used, but to keep routing happy)
     * @param int    $programme_id The programme ID we are promoting a given revision to be live.
     * @param int    $revision_id  The revision ID we are promote to the being the live output for the programme.
     */
    public function get_difference($year, $type, $programme_id = false, $revision_id = false)
    {
        if(!$programme_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

        // Get revision specified
        $programme = Programme::find($programme_id);

        if (!$programme) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $revision = $programme->find_revision($revision_id);

        if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

        $programme_attributes = $programme->attributes;
        $revision_for_diff = (array) $revision;

        // Ignore these fields which will always change
        foreach (array('id', 'created_by', 'published_by', 'created_at', 'updated_at', 'live') as $ignore) {
            unset($revision_for_diff[$ignore]);
            unset($programme_attributes[$ignore]);
        }

        $schools = School::getAsList();
        $sub = Programme::getAsList();
        $pro = Programme::getAsList();

        $revision_for_diff['related_school_ids'] = $this->splitToText($revision_for_diff['related_school_ids'],$schools);
        $programme_attributes['related_programme_ids'] = $this->splitToText($programme_attributes['related_programme_ids'],$sub);
        $revision_for_diff['related_programme_ids'] = $this->splitToText($revision_for_diff['related_programme_ids'],$sub);
        $programme_attributes['related_programme_ids'] = $this->splitToText($programme_attributes['related_programme_ids'],$pro);
        $revision_for_diff['related_programme_ids'] = $this->splitToText($revision_for_diff['related_programme_ids'],$pro);

        $differences = array_diff_assoc($programme_attributes, $revision_for_diff);

        $diff = array();

        foreach ($differences as $field => $value) {
            $diff[$field] = SimpleDiff::htmlDiff($programme_attributes[$field], $revision_for_diff[$field]);
        }

        $this->data['diff'] = $diff;
        $this->data['new'] = $revision_for_diff;
        $this->data['old'] = $programme_attributes;

        $this->data['attributes'] = Programme::getAttributesList();

        $this->data['revision'] = $revision;
        $this->data['programme'] = $programme;

        return View::make('admin.'.$this->views.'.difference',$this->data);
    }

    private function splitToText($list,$options)
    {
        if($list == '' || $list == null) return '';

        $list = explode(',',$list);
        $l_str = '';
        foreach ($list as $val) {
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
       $this->data['revisions'] = DB::table('programmes_revisions')
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
        if ($validation->fails()) {
            Messages::add('error','You tried to deactivate a post that doesn\'t exist.');

            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        } else {

            $programme = Programme::find(Input::get('id'));
            $programme->deactivate();
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
        if ($validation->fails()) {
            Messages::add('error','You tried to activate a post that doesn\'t exist.');

            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        } else {
            $programme = Programme::find(Input::get('id'));
            $programme->activate();
            Messages::add('success','Programme Activated');

            return Redirect::to($year.'/'.$type.'/'.$this->views.'');
        }
    }
}
