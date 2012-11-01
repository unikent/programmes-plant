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
          <h1>CMS Users</h1>
          <p>Use the table below to edit the users in the system.</p>
          <?php echo Messages::get_html()?>
          <?
            if($users){
              echo '<table class="table table-striped table-bordered table-condensed">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Admin</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead><tbody>
              ';
              foreach($users as $usr){
                echo '<tr>
                  <td>'.$usr->id.'</td>
                  <td>'.( $usr->admin ? 'Yes' : 'No' ).'</td>
                  <td>'.$usr->username.'</td>
                  <td>'.$usr->email.'</td>
                  <td>'.$usr->created_at.'</td>
                  <td><a class="btn btn-primary" href="'.action('users@edit', array($usr->id)).'">Edit</a> <a class="delete_toggler btn btn-danger" rel="'.$usr->id.'">Delete</a></td>
                </tr>';
              }
              echo '</tbody></table>';
            }
          ?>
          <a href="<?php echo action('users@create')?>" class="btn btn-primary right">New User</a>
        </div>

      </div>

    </div> <!-- /container -->
    <div class="modal hide fade" id="delete_user">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are You Sure?</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this user?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open('users/delete', 'POST')?>
        <a data-toggle="modal" href="#delete_user" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue" value="" />
        <input type="submit" class="btn btn-danger" value="Delete" />
        <?php echo Form::close()?>
      </div>
    </div>
    <?php echo View::make('admin.inc.scripts')->render()?>
    <script>
      $('#delete_user').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $('.delete_toggler').each(function(index,elem) {
          $(elem).click(function(){
            $('#postvalue').attr('value',$(elem).attr('rel'));
            $('#delete_user').modal('show');
          });
      });
    </script>
  </body>
</html>
