<li class='field_item' id="field-id-<?php echo $field->id?>">

<span class='title'><?php echo $field->field_name?></span>
<?php if($field->field_type == 'help'):?>
	<span class="label label-info">Help text</span> 
<?php endif;?>


<?php if(isset($field->programme_field_type) && $field->programme_field_type == $field::$types['OVERRIDABLE_DEFAULT']): ?>

	<span class="label label-info"><i class="icon-flag icon-white"></i> <?php echo __('fields.form.programme_overwrite_text_title')?></span>
	
<?php endif; ?>
<a href="<?php echo url($path.'/edit/'.$field->id);?>">Edit</a>
</li>