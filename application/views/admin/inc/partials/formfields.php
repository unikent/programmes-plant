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
          else{
            //If no curval exists and prefill is on, enter the inital value text in to the box
            if($cur_val == '' && $field->prefill == 1) $cur_val = $field->field_initval;
            $formpart = Form::$type($col, $cur_val, array('placeholder'=>$field->placeholder));
         }

    ?>
  <?php if (! isset($help)) : ?> 
  <div class="control-group">
              <?php echo Form::label($col, $field->field_name,array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo $formpart?>
                <?php if(isset($field->programme_field_type) && $field->programme_field_type == ProgrammeField::$types['OVERRIDABLE_DEFAULT']): ?>
                  <div class="info">
                    <span class="badge badge-info" rel="popover"><i class="icon-flag"></i>
                      <?php if(isset($from) && strcmp($from, 'programmes') == 0): ?>
                        <?php echo __('fields.form.programme_overwrite_text_title')?>
                      <?php elseif (isset($from) && strcmp($from, 'programmesettings') == 0): ?>
                        <?php echo __('fields.form.programme_settings_overwrite_text_title')?>
                      <?php endif; ?>
                    </span>
                    <div class="title">
                      <?php if(isset($from) && strcmp($from, 'programmes') == 0): ?>
                        <?php echo __('fields.form.programme_overwrite_text_title')?>
                      <?php elseif (isset($from) && strcmp($from, 'programmesettings') == 0): ?>
                        <?php echo __('fields.form.programme_settings_overwrite_text_title')?>
                      <?php endif; ?>
                    </div>
                    <div class="description">
                      <?php if(isset($from) && strcmp($from, 'programmes') == 0): ?>
                        <?php echo __('fields.form.programme_overwrite_text')?>
                      <?php elseif (isset($from) && strcmp($from, 'programmesettings') == 0): ?>
                        <?php echo __('fields.form.programme_settings_overwrite_text')?>
                      <?php endif; ?>
                      
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
<?php endforeach; ?>

