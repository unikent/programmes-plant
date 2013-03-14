<?php 
/*
Text Fields
*/

foreach($sections as $section_name => $section)
{
  if ($section_name != '') echo "<legend>{$section_name}</legend>";

  foreach($section as $field)
  {
      // Get Column Name
      $column_name = $field->colname;
      $type = $field->field_type;

      $current_value = '';
      if (!$create && isset($programme->$column_name)){
          $current_value = $programme->$column_name;
      }

      $tip = "{$type}_if_permitted";

      // Build select box
      switch($type){

        case 'select':
          $options_list = explode(',',$field->field_meta);
          asort($options_list);
          $form_element = Form::$tip($column_name, array_combine( $options_list, $options_list), $current_value);
          break;


        case 'checkbox':
          // Provides a default value as all empty checkboxes will otherwise result in nothing being sent and an error.
          $form_element = Form::hidden($column_name, '');
          $showLabel = true;

          if(trim($field->field_meta) == ''){  
            // Single checkbox.
            $options = array($field->field_name => 'true');
            $showLabel = false;
          } else {
            // Multiple checkboxes.
            $options = explode(',', $field->field_meta);
            asort($options);
          }
          
          // Explode comma separated options and loop through the results.
          foreach($options as $opt){
            if($opt=='') continue; //Ignore blanks (this is user inputted after all so we cant true it entirely.)
            
            // Output checkbox (name[] will be converted to array by PHP)
            // If its in current value string, select it, else leave unselected
            // WARNING: This may need to become smarter as it will not handle partal matching well.
            // (i.e. if you have math selected it would also select mathmatics just becuse maths was within it)
            $form_element .= '<label class="checkbox">'.Form::$tip($column_name.'[]', $opt, (strpos($current_value, $opt)!==false) ? true : false);
            $form_element .= ' '.(($showLabel)?$opt:'').'</label>';
          }
          break;

      case 'table_select':
        $model = $field->field_meta;
        $form_element = Form::select_if_permitted($column_name, $model::all_as_list($year, $field->empty_default_value), $current_value);
        break;

      case 'table_multiselect':
        $model = $field->field_meta;
        $form_element = ExtForm::multiselect($column_name.'[]', $model::all_as_list($year), explode(',',$current_value), array('style'=>'height:200px;width:420px;'));
        break;

      case 'help':
        break;

      default:
        if($current_value == '' && $field->prefill == 1) $current_value = $field->field_initval;
        
        $field_config = array('placeholder'=>$field->placeholder);
        //If a limit is found, add it to field.
        if(trim($field->limit) != ''){
           $field_config['data-limit'] = $field->limit;
        }

        $form_element = Form::$tip($column_name, $current_value, $field_config);
        break;  

  }
      
?>
<?php if ($type != 'help') : ?> 
	<div class="control-group">
		<?php echo Form::label($column_name, $field->field_name,array('class'=>'control-label'))?>
		<div class="controls">
		
			<?php echo $form_element?>
			
			<?php if(isset($field->programme_field_type) && $field->programme_field_type == ProgrammeField::$types['OVERRIDABLE_DEFAULT']): ?>
				<div class="overridable-badge">
					<span class="badge badge-info" rel="tooltip" data-original-title="
					<?php if(isset($from) && strcmp($from, 'programmes') == 0): ?>
					<?php echo __('fields.form.programme_overwrite_text_title')?>
					<?php elseif (isset($from) && strcmp($from, 'programmesettings') == 0): ?>
					<?php echo __('fields.form.programme_settings_overwrite_text_title')?>
					<?php endif; ?>
					">Overridable</span>
					
					<div class="description">
						<?php if(isset($from) && strcmp($from, 'programmes') == 0): ?>
						<?php if(ProgrammeSetting::get_setting($year, $field->colname) != null): ?>
						<br />i.e. <br /> <pre><?php echo ProgrammeSetting::get_setting($year, $field->colname) ?></pre>
						<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			
			<span class="help-block"><?php echo  $field->field_description; ?></span>
		
		</div>
	</div>
<?php else: ?>
    <p>
      <?php echo $field->field_description; ?>
    </p>
<?php endif; ?>
<?php
  } // End fields foreach
} // End sections foreach 
?>
