<h1><?php echo ( ! isset($values)) ? __('fields.form.add_title', array('field_name' => __('fields.form.'.$field_type))) : __('fields.form.edit_title', array('field_name' => __('fields.form.'.$field_type))); ?></h1>

<?php echo Messages::get_html()?>

<?php echo Form::open_for_files('/'.$type.'/fields/'. $field_type . '/' . ( isset($id) ? 'edit' : 'add' ), 'POST', array('class'=>'form-horizontal'));?>

<fieldset>
  <div class="control-group">
    <?php echo Form::label('title', __('fields.form.label_title'), array('class'=>'control-label'))?>
    <div class="controls">

      <?php echo Form::text('title', (isset($values)) ? $values->field_name : '' )?>
    </div>
  </div>
  <div class="control-group">
    <?php echo Form::label('type', __('fields.form.label_type'), array('class'=>'control-label'))?>
    <div class="controls">
      <?php echo Form::select('type', array('text'=>'text','textarea'=>'textarea', 'select'=>'select', 'checkbox'=>'checkbox', 'table_select'=>'select from model', 'table_multiselect'=>'multiselect from model'), (isset($values)) ? $values->field_type : '', array('onchange'=>'show_options(this);') )?>
    </div>
  </div>

  <div class="control-group" id='ext_opt'>
    <?php echo Form::label('options', "Options",array('class'=>'control-label'))?>
    <div class="controls">
      <?php echo  Form::text('options', (isset($values)) ? $values->field_meta : '' )?><br/>
      <?php echo __('fields.form.label_options'); ?>
    </div>
  </div>

  <div class="control-group">
    <?php echo Form::label('year', __('fields.form.label_description'), array('class'=>'control-label'))?>
    <div class="controls">

      <?php echo  Form::textarea('description', (isset($values)) ? $values->field_description : '' )?>
    </div>
  </div>

  <div class="control-group">
    <?php echo Form::label('initval', __('fields.form.label_start'), array('class'=>'control-label'))?>
    <div class="controls">

      <?php echo  Form::text('initval', (isset($values)) ? $values->field_initval  : '')?>
     <?php  if(!isset($values)): ?> Warning, this value will be applied to all existing records (except if the datatype is textarea). <?php endif; ?>
    </div>
  </div>

  <div class="control-group">
    <?php echo Form::label('placeholder', __('fields.form.label_placeholdertext'), array('class'=>'control-label'))?>
    <div class="controls">

      <?php echo  Form::text('placeholder', (isset($values)) ? $values->placeholder : '' )?>
    </div>
  </div>
  <?php if(strcmp($field_type, 'programmes') == 0): ?>
  <div class="control-group">

    <?php echo Form::label('programme_field_type', __('fields.form.label_programme_field_type'), array('class'=>'control-label'))?>
    <div class="controls">

      <?php echo  Form::checkbox('programme_field_type', ProgrammeField::$types['OVERRIDABLE_DEFAULT'], (isset($values)) ? (($values->programme_field_type==ProgrammeField::$types['OVERRIDABLE_DEFAULT']) ? ProgrammeField::$types['OVERRIDABLE_DEFAULT'] : false) : false)?> <?php echo __('fields.form.label_programme_field_type_text') ?>
    </div>
  </div>
<?php endif; ?>

</fieldset>

<?php  echo ( isset($values) ? Form::hidden('id', $values->id) : '' ) ?>
<br/>

<div class="form-actions">
  <input type="submit" class="btn btn-warning" value="<?php echo __('fields.form.btn.save') ?>" />
  <a class="btn" href="<?php echo url('/'.$type.'/fields/'.$field_type.'/index')?>"><?php echo __('fields.form.btn.cancel') ?></a>
</div>
