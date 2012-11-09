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
          <h1><?php echo ( $create ? 'New Programme' : 'Edit Programme' )?></h1>
          <?php if (! $create) : ?><h2><?php echo  $programmes->title ?> </h2><?php endif; ?>
          <?php echo Messages::get_html()?>
          <?php echo Form::open_for_files(URI::segment(1).'/'.URI::segment(2).'/programmes/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
          <?php if(!$create): ?>
            <?php echo  Form::hidden('programme_id', $programmes->id); ?>
          <?php endif; ?>

          <fieldset>
            <legend>Basic Information</legend>

            <div class="control-group">
              <?php echo Form::label('year', "Programme Year",array('class'=>'control-label'))?>
              <div class="controls">
                <span class="input-xlarge uneditable-input"><?php echo  ( $create ? URI::segment(1) : $programmes->year )?></span>
                <?php echo  Form::hidden('year', ( $create ? URI::segment(1) : $programmes->year ), array('class'=>'uneditable-input'))?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('title',"Programme Title",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('title',  ( Input::old('title') || ($create && !$clone) ? Input::old('title') : $programmes->title ),array('placeholder'=>'Enter programme title...'))?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('slug', __('programmes.slug'),array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('slug',  ( Input::old('slug') || ($create && !$clone) ? Input::old('slug') : $programmes->slug ),array('placeholder'=>'Enter Slug...'))?>
                <span class="help-block"><?php echo  __('programmes.slug_help') ?></span>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('award', 'Honours Type',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::select('award', $awards, isset($programmes) ? $programmes->honours : '')?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('summary', __('programmes.summary'),array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::textarea('summary',( Input::old('summary') || ($create && !$clone) ? Input::old('summary') : $programmes->summary ),array('placeholder'=>'Enter programme content...'))?>
                <span class="help-block"><?php echo  __('programmes.summary_help') ?></span>
              </div>
            </div>

<?php /*

             <div class="control-group">
              <?php echo Form::label('aa', 'Honours Type',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::select('aa', $awards, isset($programmes) ? $programmes->honours : '', array("class"=>"multiselect","multiple"=>"multiple", 'style'=>'height:200px;width:420px;'))?>
              </div>
            </div>

             <select id="countriess" class="multiselect" multiple="multiple" name="countries[]" style='height:200px;width:500px;'>
        <option value="AFG">A</option>
        <option value="ALB">B</option>
        <option value="DZA">C</option>
        <option value="AND">D</option>

      </select>

*/ ?>

</fieldset>
<fieldset>
            <legend>Administrative Settings</legend>
<div class="control-group">
              <?php //echo Form::label('campus', 'Subject', array('class'=>'control-label'))?>
              <div class="controls">
                <?php //echo Form::select('subject_id', $subjects, isset($programmes) ? $programmes->subject_id : '' )?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('campus', 'Campus',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::select('campus_id', $campuses, isset($programmes) ? $programmes->campus_id  : '' )?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('campus', 'School',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::select('school_id', $school, isset($programmes) ? $programmes->school_id  : '' )?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('campus', 'Admin School',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::select('school_adm_id', $school, isset($programmes) ?  $programmes->school_adm_id : '')?>
              </div>
            </div>
            <div class="control-group">
              <?php //echo Form::label('leaflet_ids[]', 'Leaflets',array('class'=>'control-label'))?>
              <div class="controls">
                <?php //echo ExtForm::multiselect('leaflet_ids[]', $leaflets, isset($programmes) ? explode(',',$programmes->leaflet_ids) : '', array('style'=>'height:150px;width:420px;'))?>
              </div>
            </div>
</fieldset>
<fieldset>
            <legend>Relation Settings</legend>
             <div class="control-group">
              <?php echo Form::label('rel_subjects[]', 'Related Subjects',array('class'=>'control-label'))?>
              <div class="controls">
                <?php //echo ExtForm::multiselect('rel_subjects[]', $subjects, isset($programmes) ? explode(',',$programmes->related_subject_ids) : '', array('style'=>'height:150px;width:420px;'))?>
              </div>
            </div>

             <div class="control-group">
              <?php echo Form::label('rel_programmes[]', 'Related programmes',array('class'=>'control-label'))?>
              <div class="controls">
                <?php //echo ExtForm::multiselect('rel_programmes[]', $programme_list, isset($programmes) ? explode(',',$programmes->related_programme_ids) : '', array('style'=>'height:150px;width:420px;'))?>
              </div>
            </div>

             <div class="control-group">
              <?php echo Form::label('rel_schools[]', 'Related Schools',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo ExtForm::multiselect('rel_schools[]', $school, isset($programmes) ? explode(',',$programmes->related_school_ids) : '', array('style'=>'height:150px;width:420px;'))?>
              </div>
            </div>

</fieldset>
<hr />
 <fieldset>
            <legend>Programme Information</legend>

            <?php echo View::make('admin.inc.partials.formFields', array('fields' => $fields, 'subject' => isset($programmes) ? $programmes : null,'create'=>($create && !$clone)))->render(); ?>

       </fieldset>
        <fieldset>
            <legend>Module stage content</legend>

            <div class="control-group">
              <?php echo Form::label('mod_1_title',"Stage 1 Title",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('mod_1_title',  ( Input::old('mod_1_title') || ($create && !$clone) ? Input::old('mod_1_title') : $programmes->mod_1_title ))?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('mod_1_content',"Stage 1 Content",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::textarea('mod_1_content',  ( Input::old('mod_1_content') || ($create && !$clone) ? Input::old('mod_1_content') : $programmes->mod_1_content ))?>
              </div>
            </div>

             <div class="control-group">
              <?php echo Form::label('mod_2_title',"Stage 2 Title",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('mod_2_title',  ( Input::old('mod_2_title') || ($create && !$clone) ? Input::old('mod_2_title') : $programmes->mod_2_title ))?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('mod_2_content',"Stage 2 Content",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::textarea('mod_2_content',  ( Input::old('mod_2_content') || ($create && !$clone) ? Input::old('mod_2_content') : $programmes->mod_2_content ))?>
              </div>
            </div>

             <div class="control-group">
              <?php echo Form::label('mod_3_title',"Stage 3 Title",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('mod_3_title',  ( Input::old('mod_3_title') || ($create && !$clone) ? Input::old('mod_3_title') : $programmes->mod_3_title ))?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('mod_3_content',"Stage 3 Content",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::textarea('mod_3_content',  ( Input::old('mod_3_content') || ($create && !$clone) ? Input::old('mod_3_content') : $programmes->mod_3_content ))?>
              </div>
            </div>

             <div class="control-group">
              <?php echo Form::label('mod_4_title',"Stage 4 Title",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('mod_4_title',  ( Input::old('mod_4_title') || ($create && !$clone) ? Input::old('mod_4_title') : $programmes->mod_4_title ))?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('mod_4_content',"Stage 4 Content",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::textarea('mod_4_content',  ( Input::old('mod_4_content') || ($create && !$clone) ? Input::old('mod_4_content') : $programmes->mod_4_content ))?>
              </div>
            </div>

          <div class="control-group">
              <?php echo Form::label('mod_5_title',"Stage 5 Title",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('mod_5_title',  ( Input::old('mod_5_title') || ($create && !$clone) ? Input::old('mod_5_title') : $programmes->mod_5_title ))?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('mod_5_content',"Stage 5 Content",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::textarea('mod_5_content',  ( Input::old('mod_5_content') || ($create && !$clone) ? Input::old('mod_5_content') : $programmes->mod_5_content ))?>
              </div>
            </div>

        </fieldset>

          <div class="form-actions">
            <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/programmes')?>">Back</a>
            <input type="submit" class="btn btn-primary" value="<?php echo ($create ? __('programmes.create_programme') : __('programmes.save_programme'))?>" />
          </div>
          <?php if (isset($revisions)) : ?>
            <?php echo View::make('admin.programmes.partials.revisions', array('revisions' => $revisions, 'subject' => $programmes))->render(); ?>
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
          if (event.keyCode == 13) {
            event.preventDefault();
            $(this).blur();
          }
        });

      });

    </script>
  </body>
</html>
