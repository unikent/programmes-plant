<li class='field_item' id="field-id-<?php echo $field->id?>">

<i class="icon-move"></i>

<span class='title'>
  <?php echo $field->field_name?>
</span>
<span class='type'>
  <?php echo $field->field_type?>
</span>
<span class='actions'>
  <a class="btn btn-primary right" href="<?php echo url('fields/'.$field_type.'/edit/'.$field->id);?>"><?php echo __('fields.btn.edit'); ?></a>

 <!-- <?php if($field->active == 1 ): ?>
    <a class="btn btn-danger" href='<?php echo url('fields/'.$field_type.'/deactivate');?>?id=<?php echo $field->id;?>'><?php echo __('fields.btn.deactivate'); ?></a>
  <?php else: ?>
    <a class="btn btn-success" href='<?php echo url('fields/'.$field_type.'/reactivate');?>?id=<?php echo $field->id;?>'><?php echo __('fields.btn.reactivate'); ?></a>
  <?php endif; ?>-->
</span>

</li>