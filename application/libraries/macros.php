<?php
Form::macro('actions', function($type)
{
    return '
	<div class="form-actions">
		<input type="submit" class="btn btn-warning" value="'.__($type . '.save').'" />
		<a class="btn" href="'.url(URI::segment(1).'/'.URI::segment(2).'/'.$type ).'">'. __($type.'.back').'</a>
	</div>';
});