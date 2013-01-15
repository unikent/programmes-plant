<?php

/*
* translations used on the fields setup pages
*/

return array(

    // name given to global settings in the title
    'globalsettings' => 'XCRI/KIS global settings',

    // name given to programme settings in the title
    'programmesettings' => 'Programme global settings',
    
    'programmesettings_note' => '<div class="alert alert-info"><strong>Note</strong> Some global programme fields can be overridden by values entered in each programme. These types of fields are highlighted below as <span class="label label-info"><i class="icon-flag icon-white"></i> An overridable field</span> If the value is left blank in the programme, these fields will use the default value you enter below.</div>',
    
    'programmesettings_intro' => 'This page lets you set up default values for globally applicable programme fields.',
    

    // name given to programmes in the title
    'programmes' => 'Programmes',

    // full title combines the field type (above) with the remaining title
    'title' => ':field_name field setup',

    // introduction text
    'introduction' => array(
        'globalsettings' => 'This is the listing of fields which apply to XCRI or KIS data. They may be displayed on programmes pages on the website, but they are not directly applicable to programmes.',
        'programmesettings' => 'The fields below are fields which never appear on programme edit pages in the programmes plant. They will always appear for each programme in the data output, and can never be overridden at the programme level.',
        'programmes' =>  '<p>This is the listing of fields which will appear on the programme create and edit pages.</p><p>You can drag fields around to change their ordering or to move them to a different section.</p><p>You can drag sections around to change their order too.</p><div class="alert alert-info"><p><strong>Note</strong> Empty sections will not show up on a programme edit page.</p></div>',
    ),

    // table header names for the fields listing table
    'table_header_name' => 'Name',
    'table_header_type' => 'Type',
    'table_header_view' => 'View',
    'table_sections_header_name' => 'Sections',
    'table_fields_header_name' => 'Fields',

    'btn' => array(
        'new' => 'Make a new field',
        'edit' => 'Edit',
        'deactivate' => 'Deactivate',
        'reactivate' => 'Reactivate',
        'new-section' => 'Make a new section',
    ),

    // used in the edit/add form
    'form' => array(
        'globalsettings' => 'global settings',
        'programmesettings' => 'programme settings',
        'programmes' => 'programmes',
        'add_title' => 'Add new :field_name field',
        'edit_title' => 'Edit :field_name field',
        'label_title' => 'Title',
        'label_options' => 'List options as a comma separated list',
        'label_type' => 'Type',
        'label_description' => 'Description',
        'label_start' => 'Initial value',
        'label_placeholdertext' => 'Placeholder text',
        'label_programme_field_type' => 'Overridable setting field?',
        'label_programme_field_type_text' => 'Ticking this box will create a programmes setting field that can be overwritten in individual programmes.',
        'label_limit' => 'Word limit',
        'label_limit_help_text' => 'Leave blank if this field does not require a word limit. Use 200w (for 200 words) or just 500 for 500 characters.',
        'programme_overwrite_text' => 'If left blank, this field will inherit its value from its programmes setting counterpart.',
        'programme_overwrite_text_title' => 'An overridable field',
        'programme_settings_overwrite_text' => 'This field can be overwritten in individual programmes.',
        'programme_settings_overwrite_text_title' => 'An overridable field',
        'btn' => array(
            'cancel' => 'Cancel',
            'save' => 'Save',
        ),
        'label_empty_default_value' => 'For \'select from model\' allow a blank default value of \'None\'',
    ),
    
    // the empty value used when select boxes don't need a value setting
    'empty_default_value' => 'None',

);
