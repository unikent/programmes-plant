<?php echo View::make('admin.inc.meta')->render()?>
    <title>Courses Dashboard</title>
  </head>
  <body>
    <?php echo View::make('admin.inc.header')->render()?>
    <div class="container">

      <div class="row-fluid">

        <div class="span3"> <!-- Sidebar -->
          <div class="well">
            <?php echo View::make('admin.inc.sidebar')->render()?>
          </div>
        </div> <!-- /Sidebar -->

        <div class="span9 crud">
          <h1><?php echo ( $create ? 'New Subject' : 'Edit Subject' )?></h1>
          <?php if (! $create) : ?><h2><?php echo  $subject->title ?> </h2><?php endif; ?>
          <?php echo Messages::get_html()?>
          <?php echo Form::open_for_files(URI::segment(1).'/'.URI::segment(2).'/subjects/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
          <?php if(!$create): ?> 
            <?php echo  Form::hidden('subject_id', $subject->id); ?>
          <?php endif; ?>
           
          <fieldset>
            <legend>Basic Information</legend>
            
            <div class="control-group">
              <?php echo Form::label('year', __('subjects.year'),array('class'=>'control-label'))?>
              <div class="controls">
                <span class="input-xlarge uneditable-input"><?php echo  ( $create ? URI::segment(1) : $subject->year )?></span>
                <?php echo  Form::hidden('year', ( $create ? URI::segment(1) : $subject->year ), array('class'=>'uneditable-input'))?>
              </div>
            </div>
            
            <div class="control-group">
              <?php echo Form::label('title', __('subjects.title'),array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('title',  ( Input::old('title') || $create ? Input::old('title') : $subject->title ),array('placeholder'=>'Enter Subject Title...'))?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('slug', __('subjects.slug'),array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('slug',  ( Input::old('slug') || $create ? Input::old('slug') : $subject->slug ),array('placeholder'=>'Enter Slug...'))?>
                <span class="help-block"><?php echo  __('subjects.slug_help') ?></span>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('summary', __('subjects.summary'),array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::textarea('summary',( Input::old('summary') || $create ? Input::old('summary') : $subject->summary ),array('placeholder'=>'Enter Subject Content...'))?>
                <span class="help-block"><?php echo  __('subjects.summary_help') ?></span>
              </div>
            </div>
</fieldset>
     <fieldset>
            <legend>Administrative Settings</legend>
            <div class="control-group">
              <?php echo Form::label('school_id', __('subjects.main_school_id'),array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::select('school_id', $school, isset($subject) ? $subject->main_school_id  : '' )?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('sec_school[]', __('subjects.secondary_school_ids'),array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo ExtForm::multiselect('sec_school[]', $school, isset($subject) ? explode(',',$subject->secondary_school_ids) : '', array('style'=>'height:150px;width:420px;'))?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('rel_subjects[]', __('subjects.related_subjects'),array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo ExtForm::multiselect('rel_subjects[]', $subjects, isset($subject) ? explode(',',$subject->related_subject_ids) : '', array('style'=>'height:150px;width:420px;'))?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('leaflet_ids[]', 'Leaflets',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo ExtForm::multiselect('leaflet_ids[]', $leaflets, isset($subject) ? explode(',',$subject->leaflet_ids) : '', array('style'=>'height:150px;width:420px;'))?>
              </div>
            </div>
          

    </fieldset>


 <fieldset>
            <legend><?php echo  __('subjects.content') ?></legend>


            <?php echo View::make('admin.inc.partials.formFields', array('field_meta' => $field_meta, 'subject' => isset($subject) ? $subject : null,'create'=>$create))->render(); ?>


       </fieldset>    
        

          <div class="form-actions">
            <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/subjects')?>">Go Back</a>
            <input type="submit" class="btn btn-primary" value="<?php echo ($create ? __('subjects.create_subject') : __('subjects.save_subject'))?>" />
          </div>
          <?php if (isset($revisions)) : ?>
            <?php echo View::make('admin.subjects.partials.revisions', array('revisions' => $revisions, 'subject' => $subject))->render(); ?>
          <?php endif; ?>
        </div>

      </div>

    </div> <!-- /container -->

    <?php echo View::make('admin.inc.scripts')->render()?>
      <script>
        $(function() {

        $('#promote_revision').modal({
          show:false
        }); // Start the modal

        // Populate the field with the right data for the modal when clicked
        $(".promote_toggler").click(function(){
          $('#promote_now').attr('href', $(this).attr('rel'));
          $('#promote_revision').modal('show');
        });

        $(".listbuilder_list").listbuilder({
          sortable:true,
          duplicates:false,
          delimeter: ","
        });

        $('.ui-listbuilder-input').keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            $(this).blur();
          }
        });

      });
    </script>
  </body>
</html>
