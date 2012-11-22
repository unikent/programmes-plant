 <?php 

 /*
 Text Fields


 */
  foreach($fields as $field) : 
        //Get Column Name
        $col = $field->colname;
        //get field type
        $type = $field->field_type;
        $formpart = '';//blank
        

        //If this is a create form dont get the current value, but instead use blank
         if(!$create){
            $cur_val = $subject->$col;
         }else{
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

          //else text or texarea's
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

<?php endforeach; ?>