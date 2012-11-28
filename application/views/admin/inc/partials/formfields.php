<?php 
/*
Text Fields
*/

foreach($sections as $section_name => $section){

    echo "<legend>{$section_name}</legend>";

   foreach($section as $field) {

      //Get Column Name
      $column_name = $field->colname;
      $type = $field->field_type;
      
      $current_value = '';
      if (!$create && isset($programme->$column_name))
      {
          $current_value = $programme->$column_name;
      }

      //Build select box
      if($type=='select'){

        $options_list = explode(',',$field->field_meta);
        $form_element = Form::$type($column_name, array_combine( $options_list, $options_list), $current_value);

      }else if($type=='checkbox'){  

        $form_element = Form::hidden($column_name, 'false');//Provides default value
        $form_element .= Form::$type($column_name, 'true', ($current_value=='true') ? true : false);

      }else if($type=='table_select'){

        $model = $field->field_meta;
        $form_element = Form::select($column_name, $model::all_as_list(), $current_value);

      }else if($type=='table_multiselect'){

        $model = $field->field_meta;
        $form_element = Form::select($column_name, $model::all_as_list(), $current_value, array('multiple' => 'multiple'));

      }
      else if ($type == 'help'){

        //do nothing

      }else{

         if($current_value == '' && $field->prefill == 1) $current_value = $field->field_initval;
        $form_element = Form::$type($column_name, $current_value, array('placeholder'=>$field->placeholder));  

      }


    if($type !== 'help'){
      echo "<div class='control-group'>";
        echo Form::label($column_name, $field->field_name, array('class'=>'control-label'));
        echo "<div class='controls'>";
          echo $form_element;
          echo "<span class='help-block'>{$field->field_description}</span>";
        echo "</div>";
      echo "</div>";
    }else{
      echo '<p>'.$field->field_description.'</p>';
    }
  }

}



