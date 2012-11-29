<?php

/*
* translations used on the fields setup pages
*/

return array(

    // name given to global settings in the title
    'globalsettings' => 'Global settings',

    // name given to programme settings in the title
    'programmesettings' => 'Programme settings',

    // name given to programmes in the title
    'programmes' => 'Programmes',

    // full title combines the field type (above) with the remaining title
    'title' => ':field_name field configuration',

    // introduction text
    'introduction' => array(
        'globalsettings' => 'This is the listing of fields which apply to global settings.',
        'programmesettings' => 'This is the listing of fields which apply to programme settings.',
        'programmes' =>  '<p>This is the listing of fields which will appear on the programme create and edit pages.</p><p>You can drag fields around to change their ordering or to move them to a different section.</p><p>You can drag sections around to change their order too.</p><div class="alert"><p><strong>Note</strong> Empty sections will not show up on a programme edit page.</p></div>',
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
        'programme_overwrite_text' => 'If left blank, this field will inherit its value from its programmes setting counterpart.',
        'programme_overwrite_text_title' => 'This field has a default value',
        'programme_settings_overwrite_text' => 'This field can be overwritten in individual programmes.',
        'programme_settings_overwrite_text_title' => 'An overwritable field',
        'btn' => array(
            'cancel' => 'Cancel',
            'save' => 'Save',
        ),
    ),

);
