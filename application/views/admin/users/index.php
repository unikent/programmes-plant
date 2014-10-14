  <h1>Users</h1>
  <p>Use the table below to edit the users in the system.</p>
  <?php echo Messages::get_html()?>

  <a href="<?php echo action('users@add')?>" class="btn btn-primary">New User</a>

  <p>&nbsp;</p>
  <?php
    if($users){
      echo '<table class="table table-striped table-bordered table-condensed">
      <thead>
        <tr>
          <th>Name</th>
          <th>Department</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead><tbody>
      ';
      foreach($users as $usr){
        echo '<tr>
          <td>'.$usr->fullname.' ('.$usr->username.')</td>

          <td>'.$usr->department.'</td>
          
          <td>'. HTML::mailto(Str::lower($usr->email)) .'</td>

          <td>';
          foreach($usr->roles as $role){
            switch ($role->name) {
              case 'User':
                echo 'U';
                break;
              case 'Admin':
                echo 'A';
                break;
              case 'Hyper Administrator':
                echo 'H';
                break;
              
              default:
                # do nothing
                break;
            }
          }

          echo '</td>
          
          <td><a class="btn btn-primary" href="'.action('users@edit', array($usr->id)).'">Edit</a> <a class="delete_toggler btn btn-danger" href="'.action('users@delete', array($usr->id)).'">Delete</a></td>
        </tr>';
      }
      echo '</tbody></table>';
    }
  ?>
  
</div>

