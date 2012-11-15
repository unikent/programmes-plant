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
          <h1>Programme settings - <?php echo URI::segment(1)?></h1>

          <?php echo Messages::get_html()?>
          <?php echo Form::open_for_files(URI::segment(1).'/'.URI::segment(2).'/programmesettings/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>

          <fieldset>
            <legend>Basic information</legend>

            <div class="control-group">
              <?php echo Form::label('year', "Year",array('class'=>'control-label'))?>
              <div class="controls">
                <span class="input-xlarge uneditable-input"><?php echo  ( $create ? URI::segment(1) : $programmesettings->year )?></span>
                <?php echo  Form::hidden('year', ( $create ? URI::segment(1) : $programmesettings->year ), array('class'=>'uneditable-input'))?>
              </div>
            </div>

</fieldset>
 <fieldset>
            <legend>Programme settings</legend>

            <?php echo View::make('admin.inc.partials.formFields', array('fields' => $fields, 'subject' => isset($programmesettings) ? $programmesettings : null,'create'=>$create))->render(); ?>

       </fieldset>
          <div class="form-actions">
            <input type="submit" class="btn btn-primary" value="Save" />
          </div>
          <?php if (isset($revisions)) : ?>
            <?php echo View::make('admin.programmesettings.partials.revisions', array('revisions' => $revisions, 'subject' => $programmesettings))->render(); ?>
          <?php endif; ?>
        </div>

      </div>

    </div> <!-- /container -->

    <?php echo View::make('admin.inc.scripts')->render()?>
        <script>
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
