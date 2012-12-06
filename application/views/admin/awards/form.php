<h1><?php echo ( $create ? 'New Award' : 'Edit Award' )?></h1>
<?php echo Messages::get_html()?>
<?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/awards/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
  
  <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
   
  <fieldset>
    <legend>Award Details</legend>

    <div class="control-group">
      <?php echo Form::label('name', 'Name', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('name',  ( Input::old('name') || $create ? Input::old('name') : $item->name ),array('placeholder'=>'Enter award name...'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('longname', 'Long name', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('longname',  ( Input::old('longname') || $create ? Input::old('longname') : $item->longname ),array('placeholder'=>'Enter long award name...'))?>
      </div>
    </div>

  </fieldset>
  <div class="form-actions">
    <input type="submit" class="btn btn-warning" value="<?php echo __('awards.save'); ?>" />
    <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/awards')?>">Cancel</a>
  </div>

<?php echo Form::close()?>

