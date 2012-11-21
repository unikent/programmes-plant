<?php
class ProgrammeField extends Field
{
    public static $table = 'programmes_fields';
    public static $types = array(
    								'NORMAL' => 0,
    								'DEFAULT' => 1,
    								'OVERRIDABLE_DEFAULT' => 2
    							);
}
