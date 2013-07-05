<h1><?php echo ( $create ? 'New School' : 'Edit School' )?></h1>
<?php echo Messages::get_html()?>
<?php echo Form::open('/schools/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
<?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
<fieldset>
  <legend>School Details</legend>

  <div class="control-group">
    <?php echo Form::label(__('schools.name'), 'Name', array('class'=>'control-label'))?>
    <div class="controls">
      <?php echo Form::text('name',  ( Input::old('uname') || $create ? Input::old('name') : $item->name ),array('placeholder'=>__('schools.name_placeholder')))?>
    </div>
  </div>

  <div class="control-group">
    <?php echo Form::label('faculty', __('schools.faculty'), array('class'=>'control-label'))?>
    <div class="controls">
      <?php echo Form::select('faculty', Faculty::all_as_list(), ($create ? "" : $item->faculties_id ))?>
    </div>
  </div>

</fieldset>
<?php echo Form::actions('schools')?>
<?php echo Form::close()?>