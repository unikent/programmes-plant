<h1><?php echo ( $create ? __('leaflets.form.new.header') : __('leaflets.form.edit.header') )?></h1>
<?php echo Messages::get_html()?>
<?php echo Form::open('/leaflets/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
  
  <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
   
  <fieldset>
    <legend><?php echo __('leaflets.form.details_header') ?></legend>

    <div class="control-group">
      <?php echo Form::label('name', __('leaflets.form.name.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('name',  ( Input::old('name') || $create ? Input::old('name') : $item->name ),array('placeholder' => __('leaflets.form.name.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('campus', __('leaflets.form.campus.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::select('campus', Campus::all_as_list(), ($create ? "" : $item->campuses_id ))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('tracking_code', __('leaflets.form.tracking_code.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('tracking_code',  ( Input::old('tracking_code') || $create ? Input::old('tracking_code') : $item->tracking_code ),array('placeholder' => __('leaflets.form.tracking_code.placeholder')))?>
      </div>
    </div>

  </fieldset>

<?php echo Form::actions('leaflets')?>

<?php echo Form::close()?>

