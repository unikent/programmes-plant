<?php
class Globals extends Revisionable {
     public static $timestamps = true;

     public $revision = false;

     protected $revison_type = 'global';
     protected $revision_table = 'globals_revisions';
}