<?php
Form::macro('actions', function($type, $object = null)
{
    $actions = '
	<div class="form-actions">
		<input type="submit" class="btn btn-warning" value="'.__($type . '.save').'" />
		<a class="btn" href="'.url(URLParams::get_variable_path_prefix().$type).'">'. __($type.'.back').'</a>
	</div>';

	// do this only for programmes
	if(strcmp($type, 'programmes') == 0 && isset($object) && Auth::user()->can("delete_programmes"))
	{
		$actions .= '
		<div class="alert alert-block alert-error">
			<h4>' . __($type.'.delete_title') . '</h4>
			<br />
			<p>' . __($type.'.delete_message') . '</p>
			<br />
			<a href="#delete" class="btn btn-danger popup_toggler" rel="' . action(URI::segment(1).'/'.URI::segment(2).'/'.$type . '@delete', array($object->id)) . '">'. __($type.'.delete') .'</a>
		</div>

		<div class="modal hide fade" id="delete">
		<div class="modal-header">
		  <a class="close" data-dismiss="modal">×</a>
		  <h3>' . __($type.'.delete_modal_title') . '</h3>
		</div>
		<div class="modal-body">
		  <p>' . __($type.'.delete_modal_message') . '</p>
		</div>
		<div class="modal-footer">
		    <a data-dismiss="modal" href="#" class="btn">' . __($type.'.delete_modal_cancel') . '</a>
		    <a class="btn btn-danger yes_action">' . __($type.'.delete_modal_delete') . '</a>
		</div>
		</div>';
	}
	

	return $actions;
});

Validator::register('duration', function($attribute, $value, $parameters)
{
	if (isset($parameters[0])) {
		if ($parameters[0] == 'Part-time only' && $value != 'None') return false;
		if ($parameters[0] != 'Part-time only' && $value == 'None') return false;
	}

	return true;
});

Validator::register('parttime_duration', function($attribute, $value, $parameters)
{
	if (isset($parameters[0])) {
		if ($parameters[0] == 'Full-time only' && $value != 'None') return false;
		if ($parameters[0] != 'Full-time only' && $value == 'None') return false;
	}
	
	return true;
});