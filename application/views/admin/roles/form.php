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
          <h1><?php echo ( $create ? 'New User' : 'Edit User' )?></h1>
          <?php echo Messages::get_html()?>
          <?php echo Form::open('roles/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
          <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $role->id?>" /> <?php endif; ?>
           
          <fieldset>
            <legend>User Details</legend>

            <div class="control-group">
              <?php echo Form::label('name', 'Username', array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('username',  ( Input::old('username') || $create ? Input::old('username') : $role->username ),array('placeholder'=>'Enter Role Name...'))?>
              </div>
            </div>
             <div class="control-group">
              <?php echo Form::label('fullname', 'Fullname', array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('fullname',  ( Input::old('fullname') || $create ? Input::old('fullname') : $role->fullname ),array('placeholder'=>'Enter Role Name...'))?>
              </div>
            </div>
             <div class="control-group">
              <?php echo Form::label('department', 'Department', array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('department',  ( Input::old('department') || $create ? Input::old('department') : $role->department ),array('placeholder'=>'Enter Role Name...'))?>
              </div>
            </div>
     </fieldset>
    <fieldset>
            <legend>Permissions</legend>

             <div class="control-group">
              <?php echo Form::label('isadmin', 'Is Admin?', array('class'=>'control-label'))?>
              <div class="controls">
                  <?php echo Form::checkbox('isadmin', true, ( Input::old('isadmin') || $create ? Input::old('isadmin') : $role->isadmin  ));?>
             <span class="help-inline">This will give the user permssions to edit all data, approve revsions and edit users</span>
           </div>
            </div>
             <div class="control-group">
              <?php echo Form::label('isadmin', 'Is User?', array('class'=>'control-label'))?>
              <div class="controls">

                  <?php echo Form::checkbox('isuser', true, ( Input::old('isuser') || $create ? Input::old('isuser') : $role->isuser  ));?>
              
                    <span class="help-inline">This will give the user permssions to edit and add coruse data within their department</span>
              </div>
            </div>




          </fieldset>
         

          <div class="form-actions">
            <a class="btn" href="<?php echo url('roles')?>">Go Back</a>
            <input type="submit" class="btn btn-primary" value="<?php echo ($create ? 'Create Role' : 'Save Role')?>" />
          </div>
        </div>

      </div>

    </div> <!-- /container -->

    
  </body>
</html>
