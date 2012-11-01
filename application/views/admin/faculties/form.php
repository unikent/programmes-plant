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
          <h1><?php echo ( $create ? 'New Faculty' : 'Edit Faculty' )?></h1>
          <?php echo Messages::get_html()?>
          <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/faculties/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
            
            <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $faculty->id?>" /> <?php endif; ?>
             
            <fieldset>
              <legend>Faculty Details</legend>

              <div class="control-group">
                <?php echo Form::label('name', 'Name', array('class'=>'control-label'))?>
                <div class="controls">
                  <?php echo Form::text('name',  ( Input::old('uname') || $create ? Input::old('name') : $faculty->name ),array('placeholder'=>'Enter Faculty Name...'))?>
                </div>
              </div>
            </fieldset>
            <div class="form-actions">
              <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/faculties')?>">Go Back</a>
              <input type="submit" class="btn btn-primary" value="<?php echo ($create ? 'Create Faculty' : 'Save Faculty')?>" />
            </div>

          <?php echo Form::close()?>
        </div>
      </div>

    </div> <!-- /container -->
    <div class="modal hide fade" id="delete_faculty">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are You Sure?</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this faculty?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open('faculty/delete', 'POST')?>
        <a data-toggle="modal" href="#delete_faculty" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue" value="" />
        <input type="submit" class="btn btn-danger" value="Delete" />
        <?php echo Form::close()?>
      </div>
    </div>
    <?php echo View::make('admin.inc.scripts')->render()?>
    <script>
      $('#delete_faculty').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $('.delete_toggler').each(function(index,elem) {
          $(elem).click(function(){
            $('#postvalue').attr('value',$(elem).attr('rel'));
            $('#delete_faculty').modal('show');
          });
      });
    </script>
  </body>
</html>
