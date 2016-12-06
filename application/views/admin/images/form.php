<h1><?php echo ( $create ? __('images.form.new.header') : __('images.form.edit.header') )?></h1>
<?php echo Messages::get_html()?>
<?php echo Form::open_for_files('/images/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
  
  <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
  
  <fieldset>
    <legend><?php echo __('images.form.details_header') ?></legend>

    <div class="control-group">
      <?php echo Form::label('name', __('images.form.name.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('name',  ( Input::old('name') || $create ? Input::old('name') : $item->name ),array('placeholder'=>__('images.form.name.placeholder')))?>
      </div>
    </div>

     <div class="control-group">
      <?php echo Form::label('image', __('images.form.image.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php if(! $create): ?>
          <a href="<?php echo $item->url(); ?>" target="_blank"><img src="<?php echo $item->thumb_url(); ?>" /></a>
        <?php endif; ?>
        <?php echo Form::file('image', array('placeholder'=>__('images.form.image.placeholder'), 'accept'=>'image/jpeg,image/jpg'))?>
      </div>
    </div>

       <div class="control-group">
      <?php echo Form::label('focus', __('images.form.focus.label'), array('class'=>'control-label'))?>
      <div class="controls">

          <?php echo Form::select('focus', array("top"=>"Top","bottom"=>"Bottom","center"=>"Center"),  ( Input::old('focus') || $create ? Input::old('focus') : $item->focus ))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('caption', __('images.form.caption.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('caption',  ( Input::old('caption') || $create ? Input::old('caption') : $item->caption ),array('placeholder'=>__('images.form.caption.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('title', __('images.form.title.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('title',  ( Input::old('title') || $create ? Input::old('title') : $item->title ),array('placeholder'=>__('images.form.title.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('alt', __('images.form.alt.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('alt',  ( Input::old('alt') || $create ? Input::old('alt') : $item->alt ),array('placeholder'=>__('images.form.alt.placeholder')))?>
      </div>
    </div>


    <div class="control-group">
      <?php echo Form::label('attribution_text', __('images.form.attribution_text.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('attribution_text',  ( Input::old('attribution_text') || $create ? Input::old('attribution_text') : $item->attribution_text ),array('placeholder'=>__('images.form.attribution_text.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('attribution_link', __('images.form.attribution_link.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('attribution_link',  ( Input::old('attribution_link') || $create ? Input::old('attribution_link') : $item->attribution_link ),array('placeholder'=>__('images.form.attribution_link.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('licence_link', __('images.form.licence_link.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('licence_link',  ( Input::old('licence_link') || $create ? Input::old('licence_link') : $item->licence_link ),array('placeholder'=>__('images.form.licence_link.placeholder')))?>
      </div>
    </div>

  </fieldset>

  <?php echo Form::actions('images')?>

<?php echo Form::close()?>