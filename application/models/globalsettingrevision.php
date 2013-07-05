<?php

class GlobalSettingRevision extends Revision
{
    public static $table = 'global_settings_revisions';
    protected $data_type_id = 'global_setting_id';

    /**
	 * Get display string for revision
	 * 
	 * @return string decribing revision
	 */
    public function get_identifier_string()
    {
    	$link =  action(URLParams::get_variable_path_prefix().'globalsettings/'.$this->{$this->data_type_id}.'@view_revision', array($this->id));
        return '<strong><a href="'.$link.'" target="_blank">'.$this->get_identifier().'</a></strong> created '.$this->get_created_time().' by '.$this->edits_by ;
    }

}
