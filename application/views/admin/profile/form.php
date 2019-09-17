<h1><?php echo ( $create ? 'New Student Profile' : 'Edit Student Profile' )?></h1>

<?php echo Messages::get_html()?>
<?php

$level = strtoupper(URI::segment(1));
$subject_category_class = $level . '_SubjectCategory';

?>

<?php echo Form::open('/' . URI::segment(1) . '/profile/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>

  
  <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
  
  <fieldset>
    <legend>Details</legend>

    <div class="control-group">
      <?php echo Form::label('name', __('profile.form.name.label') , array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('name',  ( Input::old('name') || $create ? Input::old('name') : $item->attributes['name'] ),array())?>
      </div>
    </div>

	  <div class="control-group">
		  <?php echo Form::label('type', __('profile.form.type.label') , array('class'=>'control-label'))?>
		  <div class="controls">
			  <?php echo Form::select('type', array('student'=>'Student', 'alumni'=>'Alumni'), ( Input::old('type') || $create ? Input::old('type') : $item->attributes['type'] ))?>
		  </div>
	  </div>

  <div class="control-group">
	  <?php echo Form::label('slug', __('profile.form.slug.label') , array('class'=>'control-label'))?>
	  <div class="controls">
		  <?php echo Form::text('slug',  ( Input::old('slug') || $create ? Input::old('slug') : $item->slug ),array())?>
	  </div>
  </div>

	  <div class="control-group">
		  <?php echo Form::label('interview_month', __('profile.form.interview_month.label'), array('class' => 'control-label'))?>
		  <div class="controls">
			  <?php echo Form::select('interview_month', array(
				  '' => 'Unknown',
				  '1' => 'January',
				  '2' => 'February',
				  '3' => 'March',
				  '4' => 'April',
				  '5' => 'May',
				  '6' => 'June',
				  '7' => 'July',
				  '8' => 'August',
				  '9' => 'September',
				  '10' => 'October',
				  '11' => 'November',
				  '12' => 'December',
			  ), ( Input::old('interview_month') || $create ? null : $item->attributes['interview_month']))?>
		  </div>
	  </div>

	  <div class="control-group">
		  <?php echo Form::label('interview_year', __('profile.form.interview_year.label'), array('class' => 'control-label'))?>
		  <div class="controls">
			  <?php
			  	$years = array( '' => 'Unknown');
			  	for($year = date('Y'); $year >= 2014; $year--) {
			  		$years[$year] = $year;
				}
			  	echo Form::select('interview_year', $years, ( Input::old('interview_year') || $create ? null : $item->attributes['interview_year']))
			  ?>
		  </div>
	  </div>

	  <div class="control-group">
		  <?php echo Form::label('banner_image_id', __('profile.form.banner_image_id.label') , array('class'=>'control-label'))?>
		  <div class="controls">
		  <?php echo Form::imagePicker('banner_image_id',( Input::old('banner_image_id') || $create ? Input::old('banner_image_id') : $item->banner_image_id ))?>
		  </div>
	  </div>

	  <div class="control-group">
		  <?php echo Form::label('profile_image_id', __('profile.form.profile_image_id.label') , array('class'=>'control-label'))?>
		  <div class="controls">
			  <?php echo Form::imagePicker('profile_image_id',( Input::old('profile_image_id') || $create ? Input::old('profile_image_id') : $item->profile_image_id ))?>
		  </div>
	  </div>

	  <div class="control-group">
		  <?php echo Form::label('course', __('profile.form.course.label') , array('class'=>'control-label'))?>
		  <div class="controls">
			  <?php echo Form::text('course',  ( Input::old('course') || $create ? Input::old('course') : $item->course ),array())?>
		  </div>
	  </div>

    <div class="control-group">
      <?php echo Form::label('subject_categories', __('profile.form.subject_categories.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php
			echo ExtForm::multiselect('subject_categories[]', $subject_category_class::all_as_list(), $create ? array() : explode(',',$item->subject_categories), array('style'=>'height:200px;width:600px;'))
		?>
	  </div>
    </div>

	  <div class="control-group">
		  <?php echo Form::label('quote', __('profile.form.quote.label') , array('class'=>'control-label'))?>
		  <div class="controls">
			  <?php echo Form::textarea('quote',  ( Input::old('quote') || $create ? Input::old('quote') : $item->quote ),array())?>
		  </div>
	  </div>

	  <div class="control-group">
		  <?php echo Form::label('video', __('profile.form.video.label') , array('class'=>'control-label'))?>
		  <div class="controls">
			  <?php echo Form::text('video',  ( Input::old('video') || $create ? Input::old('video') : $item->video ),array())?>
		  </div>
	  </div>

	  <div class="control-group">
		  <?php echo Form::label('lead', __('profile.form.lead.label') , array('class'=>'control-label'))?>
		  <div class="controls">
			  <?php echo Form::textarea('lead',  ( Input::old('lead') || $create ? Input::old('lead') : $item->lead ),array())?>
		  </div>
	  </div>

	  <div class="control-group">
		  <?php echo Form::label('content', __('profile.form.content.label') , array('class'=>'control-label'))?>
		  <div class="controls">
			  <?php echo Form::textarea('content',  ( Input::old('content') || $create ? Input::old('content') : $item->content ),array())?>
		  </div>
	  </div>
	  <div class="control-group">
		  <?php echo Form::label('links', __('profile.form.links.label') , array('class'=>'control-label'))?>
		  <div class="controls">
			  <?php echo Form::textarea('links',  ( Input::old('links') || $create ? Input::old('links') : $item->links ),array())?>
		  </div>
	  </div>

  </fieldset>

  <?php echo Form::actions('profile')?>

<?php echo Form::close()?>