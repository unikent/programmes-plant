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
          <h1>Faculties</h1>
          <p>Use the table below to edit the faculties available in this system.</p>
          <?php echo Messages::get_html()?>
          <?php
            if($faculties){
              echo '<table class="table table-striped table-bordered table-condensed">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Actions</th>
                </tr>
              </thead><tbody>
              ';
              foreach($faculties as $faculty){

                echo '<tr>
                  <td>'.$faculty->name.'</td>
                  <td><a class="btn btn-primary" href="'.action(URI::segment(1).'/'.URI::segment(2).'/faculties@edit', array($faculty->id)).'">Edit</a> <a class="delete_toggler btn btn-danger" rel="'.$faculty->id.'">Delete</a></td>
                </tr>';
              }
              echo '</tbody></table>';
            }else{
              echo '<div class="well"><p>There are no faculties in the system yet. Feel free to add one below.</p></div>';
            }
          ?>


           <div class="form-actions">
          <a href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/faculties@create')?>" class="btn btn-primary right">New Faculty</a>
        </div>
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
        <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/faculties/delete', 'POST')?>
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
