<?php if(!$create)  echo View::make('admin.revisions.partials.revision_header', array('revision' => $active_revision, 'instance' => $programmesettings, 'type'=>'programmesettings'))->render();?>


<h1><?php echo __('fields.programmesettings') ?> - <?php echo URI::segment(1)?></h1>

<?php echo Messages::get_html()?>
<?php echo __('fields.programmesettings_intro') ?>
<?php echo __('fields.programmesettings_note') ?>
<?php echo Form::open_for_files(URI::segment(1).'/'.URI::segment(2).'/programmesettings/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>

<fieldset>

  <div class="control-group">
    <?php echo Form::label('year', "Year",array('class'=>'control-label'))?>
    <div class="controls">
      <span class="input-xlarge uneditable-input"><?php echo  ( $create ? URI::segment(1) : $programmesettings->year )?></span>
      <?php echo  Form::hidden('year', ( $create ? URI::segment(1) : $programmesettings->year ), array('class'=>'uneditable-input'))?>
    </div>
  </div>

   <?php echo View::make('admin.inc.partials.formfields', array('year' => $year, 'sections' => array('' => $fields), 'programme' => isset($programmesettings) ? $programmesettings : null,'create'=>$create, 'from' => 'programmesettings'))->render(); ?>
</fieldset>

<div class="form-actions">
  <input type="submit" class="btn btn-warning" value="<?php echo __('fields.form.btn.save') ?>" />
</div>



