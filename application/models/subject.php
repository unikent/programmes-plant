<?php
class Subject extends Revisionable {
    public static $timestamps = true;

    public $revision = false;

    protected $revison_type = 'subject';
    protected $revision_table = 'subjects_revisions';
}