
          <h1><?php echo ( $create ? 'New User' : 'Edit User' )?></h1>
          <?php echo Messages::get_html()?>
          <?php echo Form::open_for_files('users/'.( $create ? 'add' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
          <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $user->id?>" /> <?php endif; ?>
           
          <fieldset>
            <legend>Information</legend>

            <div class="control-group">
              <?php echo Form::label('username', 'Username',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::text('username',  ( Input::old('username') || $create ? Input::old('username') : $user->username ), array('placeholder'=>'Enter Username...'))?>
              
                <p> Additional data will be pulled from LDAP </p>
              </div>

            </div>

            <div class="control-group">
              <?php echo Form::label('role', 'Role',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo Form::select('role', $roles, ( Input::old('role') || $create ? 3 : $user->roles[0]->id ))?>
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('subjects', 'Can manage the following subjects',array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo  ExtForm::multiselect('subjects[]', Subject::all_as_list(), ($create ?  array() : explode(',',$user->subjects)), array('style'=>'height:200px;width:460px;'));?>
              </div>
            </div>

          </fieldset>

        <div class="form-actions">
          <input type="submit" class="btn btn-warning" value="Save" />
          <a class="btn" href="<?php echo url('users')?>">Back</a>
        </div>
      </div>

