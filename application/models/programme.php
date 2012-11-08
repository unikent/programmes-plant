<?php
class Programme extends Revisionable {
     public static $timestamps = true;

     public $revision = false;

     protected $revision_type = 'programme';
     protected $revision_table = 'programme_revisions';
}