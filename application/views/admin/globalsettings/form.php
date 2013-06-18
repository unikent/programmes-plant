<?php if(!$create)  echo View::make('admin.revisions.partials.revision_header', array('revision' => $active_revision, 'instance' => $globalsettings, 'type'=>'globalsettings'))->render();?>

<?php echo Form::open_for_files(URLParams::get_variable_path_prefix().'globalsettings/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>

<div class="floating_save" data-spy="affix" data-offset-top="100">
  <div class='pull-right'>
    <input type="submit" class="btn btn-warning" value="Save">
     <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/programmes')?>">Cancel</a>
  </div>
   <strong><?php echo __('fields.globalsettings') ?> </strong>
</div>

<?php echo Messages::get_html()?>

<h1><?php echo __('fields.globalsettings') ?> - <?php echo URI::segment(1)?></h1>


<fieldset>

  <div class="control-group">
    <?php echo Form::label('year', "Year",array('class'=>'control-label'))?>
    <div class="controls">
      <span class="input-xlarge uneditable-input"><?php echo  ( $create ? URI::segment(1) : $globalsettings->year )?></span>
      <?php echo  Form::hidden('year', ( $create ? URI::segment(1) : $globalsettings->year ), array('class'=>'uneditable-input'))?>
    </div>
  </div>	


   <?php echo View::make('admin.inc.partials.formfields', array('sections' => array(''=>$fields), 'programme' => isset($globalsettings) ? $globalsettings : null,'create'=>$create, 'model' => $model))->render(); ?>
</fieldset>

<div class="form-actions">
  <input type="submit" class="btn btn-warning" value="<?php echo __('fields.form.btn.save') ?>" />
</div>

<?php echo Form::close(); ?>