<h1><?php echo ( $create ? 'New Leaflet' : 'Edit Leaflet' )?></h1>
<?php echo Messages::get_html()?>
<?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/leaflets/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
  
  <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
   
  <fieldset>
    <legend>Leaflet Details</legend>

    <div class="control-group">
      <?php echo Form::label('name', 'Name', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('name',  ( Input::old('name') || $create ? Input::old('name') : $item->name ),array('placeholder'=>'Enter Leaflet Name...'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('campus', 'Campus', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::select('campus', Campus::all_as_list(), ($create ? "" : $item->campuses_id ))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('tracking_code', 'Tracking code', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('tracking_code',  ( Input::old('tracking_code') || $create ? Input::old('tracking_code') : $item->tracking_code ),array('placeholder'=>'Enter Tracking code...'))?>
      </div>
    </div>

  </fieldset>
  <div class="form-actions">
    <input type="submit" class="btn btn-warning" value="<?php echo __('leaflets.save'); ?>" />
    <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/leaflets')?>">Cancel</a>
  </div>

<?php echo Form::close()?>

