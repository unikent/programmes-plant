<h1><?php echo __('fields.title', array('field_name' => __('fields.'.$field_type))); ?></h1>
<p style="margin-top:20px; margin-bottom:20px"><?php echo  __('fields.introduction.'.$field_type); ?></p>
<div style="margin-top:20px; margin-bottom:20px">
    <a href="<?php echo url('fields/'.$field_type.'/add')?>" class="btn btn-primary"><?php echo __('fields.btn.new'); ?></a>
</div>
<table class="table table-striped table-bordered table-condensed" width="100%">
  <thead>
    <tr>
      <th><?php echo  __('fields.table_header_name') ?></th>
      <th><?php echo  __('fields.table_header_type') ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody <?php echo $field_type == 'programmes' ? 'class="sortable-tbody"' : ''; ?>>
    <?php foreach($fields as $field) : ?>
    <tr id="field-id-<?php echo $field->id ?>">
      <td><?php echo $field_type == 'programmes' ? '<i class="icon-move"></i> ' : ''; ?><?php echo $field->field_name ?></td>
      <td><?php if(isset($field->programme_field_type) && $field->programme_field_type == ProgrammeField::$types['OVERRIDABLE_DEFAULT']): ?>
          <?php if(isset($from) && strcmp($from, 'programmes') == 0): ?>
            <span class="label label-info"><i class="icon-flag"></i> <?php echo __('fields.form.programme_overwrite_text_title')?></span>
          <?php elseif (isset($from) && strcmp($from, 'programmesettings') == 0): ?>
            <span class="label label-info"><i class="icon-flag"></i> <?php echo __('fields.form.programme_settings_overwrite_text_title')?></span>
          <?php endif; ?>
        <?php endif; ?>
        <?php echo $field->field_type ?></td>
      <td>

        <a class="btn btn-primary" href="<?php echo url('fields/'.$field_type.'/edit/'.$field->id);?>"><?php echo __('fields.btn.edit'); ?></a>

        <?php if($field->active == 1 ): ?>
          <a class="btn btn-danger" href='<?php echo url('fields/'.$field_type.'/deactivate');?>?id=<?php echo $field->id;?>'><?php echo __('fields.btn.deactivate'); ?></a>
        <?php else: ?>
          <a class="btn btn-success" href='<?php echo url('fields/'.$field_type.'/reactivate');?>?id=<?php echo $field->id;?>'><?php echo __('fields.btn.reactivate'); ?></a>
        <?php endif; ?>

      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
