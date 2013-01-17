<?php if(!$create)  echo View::make('admin.revisions.partials.revision_header', array('revision' => $active_revision, 'instance' => $globalsettings, 'type'=>'globalsettings'))->render();?>

<div class="floating_save" data-spy="affix" data-offset-top="100">
  <div class='pull-right'>
    <input type="submit" class="btn btn-warning" value="Save">
  </div>
   <strong><?php echo __('fields.globalsettings') ?> </strong>
</div>

<?php echo Messages::get_html()?>

<h1><?php echo __('fields.globalsettings') ?> - <?php echo URI::segment(1)?></h1>

<?php echo Form::open_for_files(URI::segment(1).'/'.URI::segment(2).'/globalsettings/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>

<fieldset>

  <div class="control-group">
    <?php echo Form::label('year', "Year",array('class'=>'control-label'))?>
    <div class="controls">
      <span class="input-xlarge uneditable-input"><?php echo  ( $create ? URI::segment(1) : $globalsettings->year )?></span>
      <?php echo  Form::hidden('year', ( $create ? URI::segment(1) : $globalsettings->year ), array('class'=>'uneditable-input'))?>
    </div>
  </div>	


   <?php echo View::make('admin.inc.partials.formfields', array('sections' => array(''=>$fields), 'programme' => isset($globalsettings) ? $globalsettings : null,'create'=>$create))->render(); ?>
</fieldset>

<div class="form-actions">
  <input type="submit" class="btn btn-warning" value="<?php echo __('fields.form.btn.save') ?>" />
</div>

