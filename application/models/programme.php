<?php
class Programme extends Revisionable {
     public static $timestamps = true;

     public $revision = false;

     protected $revison_type = 'programme';
     protected $revision_table = 'programmes_revisions';
}