<h1><?php echo ( $create ? 'New research staff' : 'Edit research staff' )?></h1>

<?php echo Messages::get_html()?>

<?php echo Form::open('/staff/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>

  
  <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
  
  <fieldset>
    <legend>Details</legend>

    <div class="control-group">
      <?php echo Form::label('login', __('staff.form.login.label') , array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('login',  ( Input::old('login') || $create ? Input::old('login') : $item->attributes['login'] ),array('placeholder'=>__('staff.form.login.placeholder')))?>   
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('title', __('staff.form.title.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('title',  ( Input::old('title') || $create ? Input::old('title') : $item->title ),array('placeholder'=>__('staff.form.title.placeholder')))?>
     
        <span class="help-block">Username is used to automatically pull title, email and name data from LDAP.</span>
     </div>
    </div>
     <div class="control-group">
      <?php echo Form::label('forename', __('staff.form.forename.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('forename',  ( Input::old('forename') || $create ? Input::old('forename') : $item->forename ),array('placeholder'=>__('staff.form.forename.placeholder'), 'readonly' => 'true'))?>
      </div>
    </div>
     <div class="control-group">
      <?php echo Form::label('surname', __('staff.form.surname.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('surname',  ( Input::old('surname') || $create ? Input::old('surname') : $item->surname ),array('placeholder'=>__('staff.form.surname.placeholder'), 'readonly' => 'true'))?>
      </div>
    </div>
     <div class="control-group">
      <?php echo Form::label('email', __('staff.form.email.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('email',  ( Input::old('email') || $create ? Input::old('email') : $item->email ),array('placeholder'=>__('staff.form.email.placeholder'), 'readonly' => 'true'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('role', __('staff.form.role.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('role',  ( Input::old('role') || $create ? Input::old('role') : $item->role ),array('placeholder'=>__('staff.form.role.placeholder')))?>
      </div>
    </div>
  
    <div class="control-group">
      <?php echo Form::label('profile_url', __('staff.form.subject.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::select('subject', PG_Subject::all_as_list(),  ( Input::old('subject') || $create ? Input::old('subject') : $item->subject ))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('profile_url', __('staff.form.profile_url.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('profile_url',  ( Input::old('profile_url') || $create ? Input::old('profile_url') : $item->profile_url ),array('placeholder'=>__('staff.form.profile_url.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('blurb',  __('staff.form.blurb.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::textarea('blurb',  ( Input::old('blurb') || $create ? Input::old('blurb') : $item->blurb ),array('placeholder'=>__('staff.form.blurb.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('keywords', __('staff.form.keywords.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('keywords',  ( Input::old('keywords') || $create ? Input::old('keywords') : $item->keywords ),array('placeholder'=>__('staff.form.keywords.placeholder')))?>
      </div>
    </div>

  </fieldset>

  <?php echo Form::actions('staff')?>

<?php echo Form::close()?>