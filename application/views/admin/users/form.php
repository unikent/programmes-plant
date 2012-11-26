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
          <?php echo Form::open_for_files('users/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
          <? if(!$create): ?> <input type="hidden" name="id" value="<?php echo $user->id?>" /> <? endif; ?>
           
          <fieldset>
            <legend>Basic Information</legend>

            <div class="control-group">
              <?php echo Form::label('username', 'Username',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('username',  ( Input::old('username') || $create ? Input::old('username') : $user->username ),array('placeholder'=>'Enter Username...'))?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('email', 'Email Address',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('email',  ( Input::old('email') || $create ? Input::old('email') : $user->email ),array('placeholder'=>'Enter Email Address...'))?>
              </div>
            </div>


            <div class="control-group">
              <?php echo Form::label('first_name', 'First Name',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('first_name',  ( Input::old('first_name') || $create ? Input::old('first_name') : $user->first_name ),array('placeholder'=>'Enter First Name...'))?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('last_name', 'Last Name',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('last_name',  ( Input::old('last_name') || $create ? Input::old('last_name') : $user->last_name ),array('placeholder'=>'Enter Last Name...'))?>
              </div>
            </div>


          </fieldset>
          <fieldset>
            <legend>Authentication</legend>
            <div class="control-group">
              <?php echo Form::label('password', 'Password',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::password('password', array('placeholder'=>'Enter New Password...'))?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('password_confirmation', 'Password Confirmation', array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::password('password_confirmation', array('placeholder'=>'Confirm New Password...'))?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('admin', 'Administrator', array('class'=>'control-label'))?>
              <div class="controls">
                <label class="checkbox">
                  <?php echo Form::checkbox('admin', '1', ( Input::old('admin') || $create ? Input::old('admin') : ($user->admin ? true : false)  ));?>
                </label>
              </div>
            </div>

          </fieldset>

          <?
            if($roles){
              echo '<fieldset><legend>Roles</legend><div class="control-group">';
              echo Form::label('user_list', ( $create ? 'User\'s Roles' : $user->first_name.'\'s Roles' ), array('class'=>'control-label'));
              foreach($roles as $role){
          ?>
              <div class="controls">
                <label class="checkbox">
                  <?php echo Form::checkbox('roles['.$role->id.']', '1', ( Input::old('roles['.$role->id.']') || $create ? Input::old('roles['.$role->id.']') : Koki::has_role($user,$role->id) ));?>
                  <?php echo $role->name?>
                </label>
              </div>
            </div>
          <?
              }
              echo '</fieldset>';
            }
          ?>
          <div class="form-actions">
            <a class="btn" href="<?php echo url('users')?>">Go Back</a>
            <input type="submit" class="btn btn-primary" value="<?php echo ($create ? 'Create User' : 'Save User')?>" />
          </div>
        </div>

      </div>

    </div> <!-- /container -->

    
  </body>
</html>
