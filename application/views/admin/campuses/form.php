<h1><?php echo ( $create ? 'New Campus' : 'Edit Campus' )?></h1>
<?php echo Messages::get_html()?>
<?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/campuses/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
  
  <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
   
  <fieldset>
    <legend>Campus Details</legend>

    <div class="control-group">
      <?php echo Form::label('name', 'Name', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('name',  ( Input::old('name') || $create ? Input::old('name') : $item->name ),array('placeholder'=>'Enter Leaflet Name...'))?>
      </div>
    </div>

  </fieldset>
  <div class="form-actions">
    <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/leaflets')?>">Go Back</a>
    <input type="submit" class="btn btn-primary" value="<?php echo ($create ? 'Create Create' : 'Save Campus')?>" />
  </div>

<?php echo Form::close()?>
<?php echo View::make('admin.inc.scripts')->render()?>
