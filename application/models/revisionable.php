<?php
class Revisionable extends Eloquent
{
     public static $timestamps = true;

     public $revision = false;

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
          $last = $query->lists('id');
          $last = end($last);
          $revision_attributes = $this->attributes;
          $revision_attributes['id'] = $last + 1;

          $revision_attributes[$this->revision_type.'_id'] = $this->id;

          // We don't have published by in revisions
          unset($revision_attributes['published_by']);
          unset($revision_attributes['live']);

          // Timestamp revision - the time stamp of the newly created revision should be
          // the same as the update of the main table.
          $revision_attributes['created_at'] = $this->updated_at;
          $revision_attributes['updated_at'] = $revision_attributes['created_at'];
          $revision_attributes['status'] = 'selected';

          $revision_attributes['created_by'] = Auth::user();

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
        $last = $query->lists('id');
        $last = end($last);
        $revision_attributes = $this->attributes;
        $revision_attributes['id'] = $last + 1;

        $revision_attributes[$this->revision_type.'_id'] = $this->id;

        // We don't have published by in revisions
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

     public static function all_as_list($year = false)
     {
      $options = array();
      $model = get_called_class();

      $title_field = self::get_title_field();
      
      if (!$year) {
        $data = $model::get(array('id',$title_field));
      } else {
        $data = $model::where('year','=',$year)->get(array('id',$title_field));
      }
      
      foreach ($data as $record) {$options[$record->id] = $record->$title_field;}

       return $options;
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


     private function generate_feed_index($new_programme, $path){

      //if(file_exists($path.'index.json')){
        //update just json
      //}else{
        //
        $indexData = array();
        $programmes = ProgrammeRevision::where('year','=',$new_programme->year)->where('status','=','live')->get();

        foreach($programmes as $programme){

          $indexData[] = array(
            'id' => $programme->programme_id,
            'name' => $programme->programme_title_1,
            'subject' => $programme->subject_area_1_8
          );
        }
        file_put_contents($path.'index.json',json_encode($indexData));

      //}

     }
     private function generate_feed_file($revision){
       
       //globalsettings.json
       //programmesettings.json
       //index.json
       //1.json - 360.json


       //global, settings, programme
       $data_type = get_called_class();
       $cache_location = $GLOBALS['laravel_paths']['storage'].'api'.'/ug/'.$revision->year.'/';

       if($data_type == 'Programme'){

          file_put_contents($cache_location.$revision->programme_id.'.json', json_encode($revision));
          $this->generate_feed_index($revision,$cache_location);

       }else{
          file_put_contents($cache_location.$data_type.'.json',json_encode($revision));
       }

       //ProgrammeSetting::where('year','=',$revision->year)->get();


      

       echo $cache_location;


 

     }



     //Needs urgent refactoring
     public function makeRevisionLive($revision){

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
        $this->generate_feed_file($revision);

      

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

}
