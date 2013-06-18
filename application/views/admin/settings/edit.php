<h1>System Settings</h1>

<?php echo Messages::get_html()?>

<?php echo Form::open('/settings', 'POST', array('class'=>'form-horizontal'));?>
  
   
  <fieldset>
    <legend>Current status</legend>



    <div class="control-group">
      <?php echo Form::label('ug_current_year', 'Current year (UG)', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('ug_current_year', $settings->ug_current_year ); ?>
      </div>
    </div>
    <div class="control-group">
      <?php echo Form::label('pg_current_year', 'Current year (PG)', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('pg_current_year', $settings->pg_current_year ); ?>
      </div>
    </div>


  </fieldset>

<?php echo Form::actions('leaflets')?>

<?php echo Form::close()?>

