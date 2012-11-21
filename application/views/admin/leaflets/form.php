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
        <?php echo Form::select('campus', Campus::getAsList(), ($create ? "" : $item->campuses_id ))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('tracking_code', 'Tracking code', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('tracking_code',  ( Input::old('tracking_code') || $create ? Input::old('tracking_code') : $item->tracking_code ),array('placeholder'=>'Enter Tracking code...'))?>
      </div>
    </div>
    
    <div class="control-group">
      <?php echo Form::label('main-additional', 'Main/additional', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::select('main-additional', array('1' => 'Main', '0' => 'Additional'), ($create ? "" : $item->campuses_id ))?>
      </div>
    </div>

  </fieldset>
  <div class="form-actions">
    <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/leaflets')?>">Go Back</a>
    <input type="submit" class="btn btn-primary" value="<?php echo ($create ? 'Create Leaflet' : 'Save Leaflet')?>" />
  </div>

<?php echo Form::close()?>
<?php echo View::make('admin.inc.scripts')->render()?>
