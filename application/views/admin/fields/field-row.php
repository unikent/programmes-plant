<li class='field_item' id="field-id-<?php echo $field->id?>">

<span class='title'><?php echo $field->field_name?></span>
<?php if(isset($field->programme_field_type) && $field->programme_field_type == ProgrammeField::$types['OVERRIDABLE_DEFAULT']): ?>
	<?php if(isset($from) && strcmp($from, 'programmes') == 0): ?>
		<span class="label label-info"><i class="icon-flag icon-white"></i> <?php echo __('fields.form.programme_overwrite_text_title')?></span>
	<?php elseif (isset($from) && strcmp($from, 'programmesettings') == 0): ?>
		<span class="label label-info"><i class="icon-flag icon-white"></i> <?php echo __('fields.form.programme_settings_overwrite_text_title')?></span>
	<?php endif; ?>
<?php endif; ?>
<a href="<?php echo url($type.'/fields/'.$field_type.'/edit/'.$field->id);?>">Edit</a>
</li>