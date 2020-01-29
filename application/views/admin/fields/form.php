<h1>
  <?php if (URI::segment(2) != 'immutable'): ?>
    <?php echo View::make('admin.inc.partials.type_marker')->render(); ?>
  <?php endif; ?>
  <?php echo ( ! isset($values)) ? __('fields.form.add_title', array('field_name' => __('fields.form.'.$field_type))) : __('fields.form.edit_title', array('field_name' => __('fields.form.'.$field_type))); ?>
</h1>

<?php echo Messages::get_html()?>

<?php echo Form::open_for_files($path, 'POST', array('class'=>'form-horizontal'));?>

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
      <?php echo Form::select('type', array('text'=>'text','textarea'=>'textarea', 'select'=>'select', 'checkbox'=>'checkbox', 'table_select'=>'select from model', 'table_multiselect'=>'multiselect from model', 'image'=> 'Image', 'help'=>'Help text section', 'file' => 'File'), (isset($values)) ? $values->field_type : '', array('onchange'=>'show_options(this);') )?>
    </div>
  </div>

  <div class="control-group" id='ext_opt'>
    <?php echo Form::label('options', "Options",array('class'=>'control-label'))?>
    <div class="controls">
      <?php echo  Form::text('options', (isset($values)) ? $values->field_meta : '' )?><br/>
      <?php echo __('fields.form.label_options'); ?>
    </div>
    <div class="controls">
      <?php echo  Form::checkbox('empty_default_value', '1', (isset($values)) ? $values->empty_default_value : false )?>&nbsp;<?php echo __('fields.form.label_empty_default_value'); ?>
    </div>
  </div>

  <div class="control-group">
    <?php echo Form::label('year', __('fields.form.label_description'), array('class'=>'control-label'))?>
    <div class="controls">

      <?php echo  Form::textarea('description', (isset($values)) ? $values->field_description : '' )?>
    </div>
  </div>

  <div id='form_extra_controls'>

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

    <div class="control-group">
      <?php echo Form::label('limit', __('fields.form.label_limit'), array('class'=>'control-label'))?>
      <div class="controls">

        <?php echo  Form::text('limit', (isset($values)) ? $values->limit : '' )?>
        <br><?php echo __('fields.form.label_limit_help_text'); ?>
      </div>
    </div>

    <?php if(isset($model::$types)): ?>
    <div class="control-group">

      <?php echo Form::label('programme_field_type', __('fields.form.label_programme_field_type'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo  Form::checkbox('programme_field_type', $model::$types['OVERRIDABLE_DEFAULT'], (isset($values)) ? (($values->programme_field_type==$model::$types['OVERRIDABLE_DEFAULT']) ? $model::$types['OVERRIDABLE_DEFAULT'] : false) : false)?> <?php echo __('fields.form.label_programme_field_type_text') ?>
      </div>
    </div>
  <?php endif; ?>
</div>

    <div class="control-group">
      <?php echo Form::label('permissions', __('fields.form.label_permissions'), array('class'=>'control-label'))?>
      <div class="controls">

        <?php if(!empty($roles)): ?>
        <table>
          <tr>
            <th>Role</th>
            <th>View field</th>
            <th>Edit field</th>
          </tr>

          <?php foreach($roles as $role): ?>
          <tr>
            <td><?php echo $role->name; ?></td>
            <td><?php echo Form::checkbox('permissions[R][]', $role->id, in_array($role->id, $permissions['R'])? true : false); ?></td>
            <td><?php echo Form::checkbox('permissions[W][]', $role->id, in_array($role->id, $permissions['W'])? true : false); ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
        <?php endif; ?>

        <br><?php echo __('fields.form.label_permissions_help_text'); ?>
      </div>
    </div>

  </fieldset>

<?php  echo ( isset($values) ? Form::hidden('id', $values->id) : '' ) ?>
<br/>

<div class="form-actions">
  <input type="submit" class="btn btn-warning" value="<?php echo __('fields.form.btn.save') ?>" />
  <a class="btn" href="<?php echo url(URLParams::get_variable_path_prefix().'fields/'.$field_type)?>"><?php echo __('fields.form.btn.cancel') ?></a>
</div>
