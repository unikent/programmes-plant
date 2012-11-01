<?php
class Supersubject extends Revisionable {
	public static $timestamps = true;

    public $revision = false;

    protected $revison_type = 'supersubject';
    protected $revision_table = 'supersubjects_revisions';
}