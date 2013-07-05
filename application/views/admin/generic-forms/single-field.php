<h1><?php echo ( $create ? 'New ' . Str::title(Str::singular($type)) : 'Edit ' . Str::title(Str::singular($type)) )?></h1>
<p><?php echo ( $create ? __($type . '.create_introduction') : __($type . '.edit_introduction') ); ?></p>

<?php
  $url_prefix = (!$shared) ? URLParams::$type.'/' : '';
?>  

<?php echo Messages::get_html()?>
<?php echo Form::open( $url_prefix . $type .'/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
<?php if(! $create): ?> 
<input type="hidden" name="id" value="<?php echo $item->id?>" />
<?php endif; ?>
  <div class="control-group">
    <?php echo Form::label('name', __($type . '.name'), array('class'=>'control-label'))?>
    <div class="controls">
      <?php echo Form::text('name',  ( Input::old('uname') || $create ? Input::old('name') : $item->name ),array('placeholder'=>__($type . '.name_placeholder')))?>
    </div>
  </div>

<?php echo Form::actions($type)?>

<?php echo Form::close()?>