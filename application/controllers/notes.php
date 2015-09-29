<?php

class Notes_Controller extends Admin_Controller
{

    public $restful =true;

    public $required_permissions = array('isSuperDuperUser');


    public function post_create()
    {

        $note = new Note();
        $note->programme_id = Input::get('programme_id');
        $note->note = Input::get('note');
        $note->short_note = Input::get('short_note');
        $note->save();

    }

    public function post_update()
    {

        $note = Note::find(Input::get('id'));
        $note->note = Input::get('note');
        $note->short_note = Input::get('short_note');
        $note->save();

    }



}