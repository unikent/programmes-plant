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
        'programmes' =>  '<p>This is the listing of fields which apply to programmes.</p><p>You can drag fields around within a section, or even into new sections, to change their ordering on the programme create/edit pages.</p><p>You can also drag sections around to change their order on programme pages.</p>',
    ),

    // table header names for the fields listing table
    'table_header_name' => 'Name',
    'table_header_type' => 'Type',
    'table_header_view' => 'View',
    'table_sections_header_name' => 'Sections',
    'table_fields_header_name' => 'Fields',

    'btn' => array(
        'new' => 'Add field',
        'edit' => 'Edit',
        'deactivate' => 'Deactivate',
        'reactivate' => 'Reactivate',
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
        'btn' => array(
            'cancel' => 'Cancel',
            'save' => 'Save',
        ),
    ),

);
