<h1><?php echo ( $create ? __('awards.form.new.header') : __('awards.form.edit.header') )?></h1>
<?php echo Messages::get_html()?>
<?php echo Form::open('/awards/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
  
  <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
   
  <fieldset>
    <legend><?php echo __('awards.form.details_header') ?></legend>

    <div class="control-group">
      <?php echo Form::label('name', __('awards.form.name.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('name',  ( Input::old('name') || $create ? Input::old('name') : $item->name ),array('placeholder' => __('awards.form.name.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('longname', __('awards.form.long_name.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('longname',  ( Input::old('longname') || $create ? Input::old('longname') : $item->longname ),array('placeholder' => __('awards.form.long_name.placeholder')))?>
      </div>
    </div>

  </fieldset>
  
<?php echo Form::actions('awards')?>

<?php echo Form::close()?>

