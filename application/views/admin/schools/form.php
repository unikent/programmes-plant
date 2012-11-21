<h1><?php echo ( $create ? 'New School' : 'Edit School' )?></h1>
<?php echo Messages::get_html()?>
<?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/schools/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
<?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $school->id?>" /> <?php endif; ?>
<fieldset>
  <legend>School Details</legend>

  <div class="control-group">
    <?php echo Form::label(__('schools.name'), 'Name', array('class'=>'control-label'))?>
    <div class="controls">
      <?php echo Form::text('name',  ( Input::old('uname') || $create ? Input::old('name') : $school->name ),array('placeholder'=>__('schools.name_placeholder')))?>
    </div>
  </div>

  <div class="control-group">
    <?php echo Form::label('faculty', __('schools.faculty'), array('class'=>'control-label'))?>
    <div class="controls">
      <?php echo Form::select('faculty', Faculty::getAsList(), ($create ? "" : $school->faculties_id ))?>
    </div>
  </div>

</fieldset>
<div class="form-actions">
  <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/schools')?>"><?php echo __('schools.back'); ?></a>
  <input type="submit" class="btn btn-primary" value="<?php echo ($create ? __('schools.create') : __('schools.save'))?>" />
</div>
<?php echo Form::close()?>