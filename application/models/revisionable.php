<?php
class Revisionable extends Eloquent
{
     public static $timestamps = true;

     public $revision = false;

     /**
      * This is an in memory cache used by the all_as_list method for additional speed.
      */
     public static $list_cache = false;

     public function user()
     {
          return $this->belongs_to('User','user_id');
     }

     /**
      * Overwrite Eloquent save function with our own version that allows for revisions.
      *
      * @return $result The result of this save.
      */
     public function save()
     {
      if ( ! $this->dirty()) return true;

      // Time stamp the entry
      $this->timestamp();

      // If the subject exists we want to create a new version of it in our revision table.
      // If they have set it to "live" then we want to handle this, but pushing this revision into
      // the live table.
      if ($this->exists) {
        // If we have $this->revision then we loaded up a revision of this class and we don't save a
        // revision.
        // @todo Abstract this.
        if (! $this->revision) {
          $query = DB::table($this->revision_table);

          // Establish the next ID in the revisions table.
          $revision_attributes = $this->attributes;
          $revision_attributes[$this->revision_type.'_id'] = $this->id;

          // We don't have published by in revisions
          unset($revision_attributes['id']);
          unset($revision_attributes['published_by']);
          unset($revision_attributes['live']);

          // Timestamp revision - the time stamp of the newly created revision should be
          // the same as the update of the main table.
          $revision_attributes['created_at'] = $this->updated_at;
          $revision_attributes['updated_at'] = $revision_attributes['created_at'];
          $revision_attributes['status'] = 'selected';

          if ($revision_attributes['created_by'] == null)
          {
              $revision_attributes['created_by'] = Auth::user();
          }

          // Deactivate any previosuly selected drafts
          $r_model = $this->revision_model;
          $r_model::where($this->revision_type.'_id','=',$this->id)->where('status','=','selected')->update(array('status'=>'draft'));

          // Add new revision
          $revision_id = $query->insert_get_id($revision_attributes, $this->sequence());

          //Update selected item
          if (sizeof($this->get_dirty())>0) {
            $query = $this->query()->where(static::$key, '=', $this->get_key());
            $result = $query->update($this->get_dirty()) === 1;
          }

           

          $this->exists = $result = is_numeric($this->get_key());
        } else {
          $query = DB::table($this->revision_table)
            ->where('id', '=', $this->revision->id)
            ->update((array) $this->revision);
        }



      }

      // The subject does not exist, so we create it.
      else {
        // By Default this subject is inactive!
        $this->live = 0;

        $id = $this->query()->insert_get_id($this->attributes, $this->sequence());

        $this->set_key($id);

        $this->exists = $result = is_numeric($this->get_key());

        // Save a revision  of this
        $query = DB::table($this->revision_table);

        // Establish the next ID in the revisions table.
        $revision_attributes = $this->attributes;
        $revision_attributes[$this->revision_type.'_id'] = $this->id;

        // We don't have published by in revisions
        unset($revision_attributes['id']);
          unset($revision_attributes['published_by']);
          unset($revision_attributes['live']);

        // Timestamp revision - the time stamp of the newly created revision should be
        // the same as the update of the main table.
        $revision_attributes['created_at'] = $this->updated_at;
        $revision_attributes['updated_at'] = $revision_attributes['created_at'];
        $revision_attributes['status'] = 'selected';

        // Add a revision
        $revision_id = $query->insert_get_id($revision_attributes, $this->sequence());
      }

      // Set the original attributes to match the current attributes so the model will not be viewed
      // as being dirty and subsequent calls won't hit the database.
      $this->original = $this->attributes;

      return $result;
     }

     /**
      * Returns the revisions of a given subject as an array.
      *
      * @return array|bool $results Either return an array of revisions or false.
      */
     public function get_revisions()
     {
      if (! $this->exists) {
        return false;
      }

      $results = DB::table($this->revision_table)
        ->where($this->revision_type.'_id', '=', $this->get_key())
        ->order_by('created_at', 'desc')
        ->get();

      if ($results) {
        return $results;
      } else {
        return false;
      }
     }

     /**
      * Find a revision of this subject.
      *
      * @param int $id The ID of the revision to pull.
      * @return object|bool Either the revision object or false if the revision is not found.
      */
     public function find_revision($id)
     {
        $revision = DB::table($this->revision_table)
            ->where('id', '=', $id)
            ->first();

          if ($revision) {
            $this->revision = $revision;

            return $revision;
          } else {
            return false;
          }
     }

     /**
      * Gives a flat array of id => item_title for all items.
      * 
      * Used generally to create select dropdowns.
      * 
      * This has two levels of caching. First a database lookup cache, then an in memory cache.
      * 
      * This is done for application performance.
      * 
      * @param string $year The year from which to get the array.
      * @return array $options List of items in the format id => item_title.
      */
     public static function all_as_list($year = false)
     {
        $model = get_called_class();

        $cache_key = "$model-$year-options-list";

        if (isset(static::$list_cache[$cache_key])) return static::$list_cache[$cache_key];

        return static::$list_cache[$cache_key] = Cache::remember($cache_key, function() use ($year, $model)
        {
          $options = array();

          $title_field = $model::get_title_field();

          if (! $year)
          {
            $data = $model::get(array('id', $title_field));
          }
          else 
          {
            $data = $model::where('year','=', $year)->get(array('id',$title_field));
          }

          foreach ($data as $record)
          {
            $options[$record->id] = $record->$title_field;
          }

            return $options;
        }, 2628000); // Cache forever.
     }

     public static function getAttributesList($year = false)
     {
      $options = array();

      $model = get_called_class();

      $model .= 'Field';

      if (!$year) {
        $data = $model::get();
      } else {
        $data = $model::where('year','=',$year)->get();
      }

      foreach ($data as $record) {$options[$record->colname] = $record->field_name;}

       return $options;
     }

  /**
   * 
   */
  private function generate_feed_index($new_programme, $path)
  {
    $index_file = $path.'index.json';
  
    $title_field = Programme::get_title_field();
    $slug_field = Programme::get_slug_field();
    $withdrawn_field = Programme::get_withdrawn_field();
    $suspended_field = Programme::get_suspended_field();
    $subject_to_approval_field = Programme::get_subject_to_approval_field();
    $new_programme_field = Programme::get_new_programme_field();
    $mode_of_study_field = Programme::get_mode_of_study_field();
    $ucas_code_field = Programme::get_ucas_code_field();
    $index_data = array();
    $programmes = ProgrammeRevision::where('year','=',$new_programme->year)
                      ->where('status','=','live')
                      ->where($withdrawn_field,'!=','true')
                      ->where($suspended_field,'!=','true')
                      ->get();

    foreach($programmes as $programme)
    {
      $index_data[$programme->programme_id] = array(
        'id' => $programme->programme_id,
        'name' => $programme->$title_field,
        'slug' => $programme->$slug_field,
        'award' => $programme->award->name,
        'subject' => $programme->subject_area_1->name,
        'main_school' => $programme->administrative_school->name,
        'secondary_school' => $programme->additional_school->name,
        'campus' => $programme->location->name,
        'new_programme' => $programme->$new_programme_field,
        'subject_to_approval' => $programme->$subject_to_approval_field,
        'mode_of_study' => $programme->$mode_of_study_field,
        'ucas_code' => $programme->$ucas_code_field
        );
    }
    
    file_put_contents($index_file, json_encode($index_data));

  }

  /**
   * Generate all the necessary JSON files that are used in our API. These are:
   * GlobalSettings.json
   * ProgrammeSettings.json
   * Index.json -- this function calls $this->generate_feed_index() to generate this
   * {programme_id}.json
   * 
   * If we're saving a programme, we generate the programme's json file as well as update our index file to reflect the changes.
   * All other revisionables are 
   * 
   * @param $revision The revision to base our saving on
   *
   */
  private function generate_feed_file($revision)
  {
    //global, settings, programme
    $data_type = get_called_class();
    $cache_location = path('storage') .'api'.'/ug/'.$revision->year.'/';

    // if our $cache_location isnt available, create it
    if (!is_dir($cache_location)) {
      mkdir($cache_location, 0755, true);
    }

    // if we're saving a programme
    if($data_type == 'Programme')
    {
      file_put_contents($cache_location.$revision->programme_id.'.json', json_encode($revision->to_array()));
      $this->generate_feed_index($revision,$cache_location);

    }else{
      file_put_contents($cache_location.$data_type.'.json', json_encode($revision->to_array()));
    }

  }

  /**
   * This function makes the specified revision live
   * 
   * @param $revision The revision to make live
   */
  public function makeRevisionLive($revision)
  {

    foreach ($this->attributes as $key => $attribute) {
      if(in_array($key, array('id', 'created_by', 'published_by', 'created_at', 'updated_at', 'live'))) continue;
      $this->$key = $revision->$key;
    }

    $this->published_by = Auth::user();
    $this->live = 1;

    $model = $this->revision_model;
    $model::where($this->revision_type.'_id','=',$this->id)->where('status','=','live')->update(array('status'=>'draft'));

    //Make new item "live"
    $r = $model::find($revision->id);
    $r->status = 'live';
    $r->save();

    //update feed file
    $this->generate_feed_file($r);
  }

     //Needs urgent refactoring
     public function revertToRevision($revision){
       foreach ($this->attributes as $key => $attribute) {
          if(in_array($key, array('id', 'created_by', 'published_by', 'created_at', 'updated_at', 'live'))) continue;
          $this->$key = $revision->$key;
        }

        // Save
        if (sizeof($this->get_dirty())>0) {
          $query = $this->query()->where(static::$key, '=', $this->get_key());
          $result = $query->update($this->get_dirty()) === 1;
        }
        
        $model = $this->revision_model;
        // reject later revisions
        $model::where($this->revision_type.'_id','=',$this->id)->where('id','>',$revision->id)->update(array('status'=>'rejected'));

        // Make new Revsion Live!
        $r = $model::find($revision->id);
        if($r->status != 'live')$r->status = 'selected';
        $r->save();

     }


     public function useRevision($revision)
     {
        // Create live from revison
        foreach ($this->attributes as $key => $attribute) {

          if(in_array($key, array('id', 'created_by', 'published_by', 'created_at', 'updated_at', 'live'))) continue;

          $this->$key = $revision->$key;
        }
        $this->published_by = Auth::user();
        $this->live = 1;

        // Save
        if (sizeof($this->get_dirty())>0) {
          $query = $this->query()->where(static::$key, '=', $this->get_key());
          $result = $query->update($this->get_dirty()) === 1;
        }

        $model = $this->revision_model;

        // Unlive previous revsion
        $model::where($this->revision_type.'_id','=',$this->id)->where('status','!=','draft')->update(array('status'=>'draft'));

        // Make new Revsion Live!
        $r = $model::find($revision->id);
        $r->status = 'live';
        $r->save();
     }

     public function deactivate()
     {
        // Remove live option
        $this->live = 0;
        if (sizeof($this->get_dirty())>0) {
          $query = $this->query()->where(static::$key, '=', $this->get_key());
          $result = $query->update($this->get_dirty()) === 1;
        }

        $model = $this->revision_model;

        // Make current live draft "selected"
        $model::where($this->revision_type.'_id','=',$this->id)->where('status','=','live')->update(array('status'=>'selected'));
     }

     public function activate()
     {
        // Add live option
        $this->live = 1;
        if (sizeof($this->get_dirty())>0) {
          $query = $this->query()->where(static::$key, '=', $this->get_key());
          $result = $query->update($this->get_dirty()) === 1;
        }

        $model = $this->revision_model;

        // Make current live draft "selected"
        $model::where($this->revision_type.'_id','=',$this->id)->where('status','=','selected')->update(array('status'=>'live'));
     }


    /**
     * Removes the automatically generate field ids from our field names
     * 
     * @param $record Record to remove field ids from
     * @return $new_record Record with field ids removed
     */
    public static function remove_ids_from_field_names($record)
    {
        $new_record = array();
        foreach ($record as $name => $value) {
          $new_record[preg_replace('/_\d{1,3}$/', '', $name)] = $value;
        }

        return $new_record;
    }

}
