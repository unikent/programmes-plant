<?php

class GlobalSettingField extends Field
{
    public static $table = 'global_settings_fields';

    // Schemas to update when fields are added
    protected static $schemas = array('GlobalSetting', 'GlobalSettingRevision');
}

