<h1><?php echo __('fields.title', array('field_name' => __('fields.globalsettings'))); ?></h1>

<p style="margin-top:20px; margin-bottom:20px"><?php echo  __('fields.introduction.globalsettings'); ?></p>

<div style="margin-top:20px; margin-bottom:20px">
    <a href="<?php echo url($path.'/add')?>" class="btn btn-primary"><?php echo __('fields.btn.new'); ?></a>
</div>

<table class="table table-striped table-bordered table-condensed" width="100%">
  <thead>
    <tr>
      <th><?php echo  __('fields.table_header_name') ?></th>
      <th><?php echo  __('fields.table_header_type') ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($fields as $field) : ?>
    <tr id="field-id-<?php echo $field->id ?>">
      <td><?php echo $field->field_name ?></td>
      <td><?php echo $field->field_type ?></td>
      <td>
        <a class="btn btn-primary" href="<?php echo url($path.'/edit/'.$field->id);?>"><?php echo __('fields.btn.edit'); ?></a>
        <?php if($field->active == 1 ): ?>
          <a class="btn btn-danger" href='<?php echo url($path.'/deactivate');?>?id=<?php echo $field->id;?>'><?php echo __('fields.btn.deactivate'); ?></a>
        <?php else: ?>
          <a class="btn btn-success" href='<?php echo url($path.'/reactivate');?>?id=<?php echo $field->id;?>'><?php echo __('fields.btn.reactivate'); ?></a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
