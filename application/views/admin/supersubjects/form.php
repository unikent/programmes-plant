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
          <h1><?php echo ( $create ? 'New Supersubject' : 'Edit Supersubject' )?></h1>
          <?php echo Messages::get_html()?>
          <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/supersubjects/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
            
            <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $supersubject->id?>" /> <?php endif; ?>
             
            <fieldset>
              <legend>Basic Information</legend>

              <div class="control-group">
                <?php echo Form::label('year', __('supersubjects.year'),array('class'=>'control-label'))?>
                <div class="controls">
                  <span class="input-xlarge uneditable-input"><?php echo  ( $create ? URI::segment(1) : $supersubject->year )?></span>
                  <?php echo  Form::hidden('year', ( $create ? URI::segment(1) : $supersubject->year ), array('class'=>'uneditable-input'))?>
                </div>
              </div>

              <div class="control-group">
                <?php echo Form::label('title', 'Title', array('class'=>'control-label'))?>
                <div class="controls">
                  <?php echo Form::text('title',  ( Input::old('utitle') || $create ? Input::old('title') : $supersubject->title ),array('placeholder'=>'Enter Supersubject Name...'))?>
                </div>
              </div>
            </fieldset>

             <fieldset>
                    <legend>Administrative Settings</legend>

                    <div class="control-group">
                      <?php echo Form::label('linked_subjects[]', 'Linked Subjects',array('class'=>'control-label'))?>
                      <div class="controls">
                        <?php echo ExtForm::multiselect('linked_subjects[]', $subjects, isset($supersubject) ? explode(',',$supersubject->subject_ids) : '', array("class"=>"multiselect","multiple"=>"multiple", 'style'=>'height:210px;width:420px;'))?>
                      </div>
                    </div>

                    <div class="control-group">
                      <?php echo Form::label('linked_programmes[]', 'Linked Programmes',array('class'=>'control-label'))?>
                      <div class="controls">
                        <?php echo ExtForm::multiselect('linked_programmes[]', $programmes, isset($supersubject) ? explode(',',$supersubject->programme_ids) : '', array("class"=>"multiselect","multiple"=>"multiple", 'style'=>'height:210px;width:420px;'))?>
                      </div>
                    </div>
                  

            </fieldset>

            <div class="form-actions">
              <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/supersubjects')?>">Go Back</a>
              <input type="submit" class="btn btn-primary" value="<?php echo ($create ? 'Create Supersubject' : 'Save Supersubject')?>" />
            </div>

            <?php if (isset($revisions)) : ?>
              <?php echo View::make('admin.supersubjects.partials.revisions', array('revisions' => $revisions, 'supersubject' => $supersubject))->render(); ?>
            <?php endif; ?>

          <?php echo Form::close()?>
        </div>
      </div>

    </div> <!-- /container -->
    <div class="modal hide fade" id="delete_supersubject">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are You Sure?</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this supersubject?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open('supersubject/delete', 'POST')?>
        <a data-toggle="modal" href="#delete_supersubject" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue" value="" />
        <input type="submit" class="btn btn-danger" value="Delete" />
        <?php echo Form::close()?>
      </div>
    </div>
    <?php echo View::make('admin.inc.scripts')->render()?>
    <script>
      $('#delete_supersubject').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $('.delete_toggler').each(function(index,elem) {
          $(elem).click(function(){
            $('#postvalue').attr('value',$(elem).attr('rel'));
            $('#delete_supersubject').modal('show');
          });
      });

      $('#promote_revision').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $(".promote_toggler").click(function(){
        $('#promote_now').attr('href', $(this).attr('rel'));
        $('#promote_revision').modal('show');
      });
    </script>
  </body>
</html>
