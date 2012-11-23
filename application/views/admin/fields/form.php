
          <h1><?php echo ( ! isset($values)) ? __('fields.form.add_title', array('field_name' => __('fields.form.'.$field_type))) : __('fields.form.edit_title', array('field_name' => __('fields.form.'.$field_type))); ?></h1>

          <?php echo Messages::get_html()?>

          <?php echo Form::open_for_files('/fields/'.$field_type.'/add', 'POST', array('class'=>'form-horizontal'));?>

          <fieldset>
              <legend>&nbsp;</legend>

            <div class="control-group">
              <?php echo Form::label('title', __('fields.form.label_title'), array('class'=>'control-label'))?>
              <div class="controls">

                <?php echo Form::text('title', (isset($values)) ? $values->field_name : '' )?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('type', __('fields.form.label_type'), array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo  Form::select('type', array('text'=>'text','textarea'=>'textarea','select'=>'select', 'checkbox'=>'checkbox'), (isset($values)) ? $values->field_type : '', array('onchange'=>'tryShow(this);') )?>
              </div>
            </div>

            <div class="control-group" id='ext_opt' <?php if (isset($values) && $values->field_type=='select') {} else { echo 'style="display:none;"'; }?>>
              <?php echo Form::label('options', "Options",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo  Form::text('options', (isset($values)) ? $values->field_meta : '' )?><br/>
                <?php echo __('fields.form.label_options'); ?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('year', __('fields.form.label_description'), array('class'=>'control-label'))?>
              <div class="controls">

                <?php echo  Form::textarea('description', (isset($values)) ? $values->field_description : '' )?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('initval', __('fields.form.label_start'), array('class'=>'control-label'))?>
              <div class="controls">

                <?php echo  Form::text('initval', (isset($values)) ? $values->field_initval  : '')?> <?php echo Form::checkbox('prefill','1', (isset($values)) ? ($values->prefill==1) ? 1 : 0 : false)?> (Pre fill forms?)<br/>
               <?php  if(!isset($values)): ?> Warning, this value will be applied to all existing records (except if the datatype is textarea). <?php endif; ?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('placeholder', __('fields.form.label_placeholdertext'), array('class'=>'control-label'))?>
              <div class="controls">

                <?php echo  Form::text('placeholder', (isset($values)) ? $values->placeholder : '' )?>
              </div>
            </div>

          </fieldset>

          <?php  echo ( isset($values) ? Form::hidden('id', $values->id) : '' ) ?>
          <br/>

          <div class="form-actions">
            <a class="btn btn-warning" href="<?php echo url('/fields/'.$field_type.'/index')?>"><?php echo __('fields.form.btn.cancel') ?></a>
            <input type="submit" class="btn btn-primary" value="<?php echo __('fields.form.btn.save') ?>" />
          </div>

       