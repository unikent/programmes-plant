<?php
class programme extends Revisionable
{
     public static $timestamps = true;

     public $revision = false;

     protected $revision_type = 'programme';
     protected $revision_table = 'programmes_revisions';
}
