<?php 
 /*
 Text Fields
 */
 
 foreach($sections as $section_name => $section) : 
 
 ?>
 <h2><?php echo $section_name ?></h2>
 <?php
 
  foreach($section as $field) : 
        //Get Column Name
        $col = $field->colname;
        //get field type
        $type = $field->field_type;
        $formpart = '';//blank
        

        //If this is a create form dont get the current value, but instead use blank
         if (!$create)
         {
            $cur_val = $programme->$col;
         }
         else
         {
           $cur_val = '';
         }
         //Build select box
         if($type=='select'){
            $optlist = explode(',',$field->field_meta);
            $formpart = Form::$type($col, array_combine($optlist,$optlist), $cur_val);
        //elseif build checkbox
         }else if($type=='checkbox'){  

           $formpart = Form::hidden($col, 'false');//Provides default value
           $formpart .= Form::$type($col, 'true', ($cur_val=='true') ? true : false);

           //elseif build table_select
         }else if($type=='table_select'){
            $model = $field->field_meta;
            $formpart = Form::select($col, $model::getAsList(), $cur_val);
            //elseif build table_multiselect
         }else if($type=='table_multiselect'){
            $model = $field->field_meta;
            $formpart = Form::select($col, $model::getAsList(), $cur_val, array('multiple' => 'multiple'));
          }
          else if ($type == 'help'){
                      $help = true;
          }
          }else{
            //If no curval exists and prefill is on, enter the inital value text in to the box
            if($cur_val == '' && $field->prefill == 1) $cur_val = $field->field_initval;
            $formpart = Form::$type($col, $cur_val, array('placeholder'=>$field->placeholder));
         }

    ?>
  <div class="control-group">
              <?php echo Form::label($col, $field->field_name,array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo $formpart?>
                <span class="help-block"><?php echo  $field->field_description; ?></span>
              </div>
            </div>
<?php else: ?>
    <p>
      <?php echo $field->field_description; ?>
    </p>
<?php endif; ?>
<?php endforeach; ?>