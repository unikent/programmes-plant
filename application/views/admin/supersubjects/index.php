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

        <div class="span9">
          <h1>Supersubjects</h1>
          <p>Use the table below to edit the supersubjects available in this system.</p>
          <?php echo Messages::get_html()?>
          <?php
            if($supersubjects){
              echo '<table class="table table-striped table-bordered table-condensed">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Actions</th>
                </tr>
              </thead><tbody>
              ';
              foreach($supersubjects as $supersubject){

                echo '<tr>
                  <td>'.$supersubject->title.'</td>
                  <td><a class="btn btn-primary" href="'.action(URI::segment(1).'/'.URI::segment(2).'/supersubjects@edit', array($supersubject->id)).'">Edit</a>';
                  ?>
                  <?php if( $supersubject->live == 1): ?>
                      <a class="deactivate_toggler btn btn-danger" rel="<?php echo  $supersubject->id ?>">Deactivate</a>
                    <?php else: ?>
                      <a class="activate_toggler btn btn-success" rel="<?php echo  $supersubject->id ?>">Activate</a>
                    <?php endif; 



               echo '</tr>';
              }
              echo '</tbody></table>';
            }else{
              echo '<div class="well"><p>There are no supersubjects in the system yet. Feel free to add one below.</p></div>';
            }
          ?>


           <div class="form-actions">
          <a href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/supersubjects@create')?>" class="btn btn-primary right">New Supersubject</a>
        </div>
        </div>

      </div>

    </div> <!-- /container -->


    <div class="modal hide fade" id="deactivate_subject">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are You Sure?</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this subject?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/supersubjects/deactivate', 'POST')?>
        <a data-toggle="modal" href="#deactivate_subject" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue" value="" />
        <input type="submit" class="btn btn-danger" value="Deactivate" />
        <?php echo Form::close()?>
      </div>
    </div>

    <div class="modal hide fade" id="activate_subject">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are You Sure?</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to make the currently selected revision live?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/supersubjects/activate', 'POST')?>
        <a data-toggle="modal" href="#activate_subject" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue2" value="" />
        <input type="submit" class="btn btn-danger" value="Activate" />
        <?php echo Form::close()?>
      </div>
    </div>



    <?php echo View::make('admin.inc.scripts')->render()?>
    <script>
        $('#deactivate_subject').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $('.deactivate_toggler').each(function(index,elem) {
          $(elem).click(function(){
            $('#postvalue').attr('value',$(elem).attr('rel'));
            $('#deactivate_subject').modal('show');
          });
      });

      $('#activate_subject').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $('.activate_toggler').each(function(index,elem) {
          $(elem).click(function(){
            $('#postvalue2').attr('value',$(elem).attr('rel'));
            $('#activate_subject').modal('show');
          });
      });
    </script>
  </body>
</html>
