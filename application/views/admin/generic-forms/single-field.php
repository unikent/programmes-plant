<h1><?php echo ( $create ? 'New ' . Str::title(Str::singular($type)) : 'Edit ' . Str::title(Str::singular($type)) )?></h1>
<p><?php echo ( $create ? __($type . '.create_introduction') : __($type . '.edit_introduction') ); ?></p>
<?php echo Messages::get_html()?>
<?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/'. $type .'/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
<?php if(! $create): ?> 
<input type="hidden" name="id" value="<?php echo $item->id?>" />
<?php endif; ?>
<fieldset>
  <legend><?php echo Str::title(Str::singular($type)); ?> Details</legend>
  <div class="control-group">
    <?php echo Form::label('name', __($type . '.name'), array('class'=>'control-label'))?>
    <div class="controls">
      <?php echo Form::text('name',  ( Input::old('uname') || $create ? Input::old('name') : $item->name ),array('placeholder'=>__($type . '.name_placeholder')))?>
    </div>
  </div>
</fieldset>
<div class="form-actions">
  <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/' )?>"><?php echo __($type . '.back'); ?></a>
  <input type="submit" class="btn btn-primary" value="<?php echo ($create ? __($type . '.create') : __($type . '.edit')); ?>" />
</div>
<?php echo Form::close()?>