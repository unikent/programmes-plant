<?php
Form::macro('actions', function($type, $object)
{
    $actions = '
	<div class="form-actions">
		<input type="submit" class="btn btn-warning" value="'.__($type . '.save').'" />
		<a class="btn" href="'.url(URI::segment(1).'/'.URI::segment(2).'/'.$type ).'">'. __($type.'.back').'</a>
	</div>

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

	return $actions;
});