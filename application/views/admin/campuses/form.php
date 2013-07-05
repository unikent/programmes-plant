<h1><?php echo ( $create ? __('campuses.form.new.header') : __('campuses.form.edit.header') )?></h1>
<?php echo Messages::get_html()?>
<?php echo Form::open('/campuses/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
  
  <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
  
  <fieldset>
    <legend><?php echo __('campuses.form.details_header') ?></legend>

    <div class="control-group">
      <?php echo Form::label('name', __('campuses.form.name.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('name',  ( Input::old('name') || $create ? Input::old('name') : $item->name ),array('placeholder'=>__('campuses.form.name.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('title', __('campuses.form.title.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('title',  ( Input::old('title') || $create ? Input::old('title') : $item->title ),array('placeholder'=>__('campuses.form.title.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('address_1', __('campuses.form.address_1.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('address_1',  ( Input::old('address_1') || $create ? Input::old('address_1') : $item->address_1 ),array('placeholder'=>__('campuses.form.address_1.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('address_2', __('campuses.form.address_2.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('address_2',  ( Input::old('address_2') || $create ? Input::old('address_2') : $item->address_2 ),array('placeholder'=>__('campuses.form.address_2.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('address_3', __('campuses.form.address_3.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('address_3',  ( Input::old('address_3') || $create ? Input::old('address_3') : $item->address_3 ),array('placeholder'=>__('campuses.form.address_3.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('town', __('campuses.form.town.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('town',  ( Input::old('town') || $create ? Input::old('town') : $item->town ),array('placeholder'=>__('campuses.form.town.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('postcode', __('campuses.form.postcode.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('postcode',  ( Input::old('postcode') || $create ? Input::old('postcode') : $item->postcode ),array('placeholder'=>__('campuses.form.postcode.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('email', __('campuses.form.email.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('email',  ( Input::old('email') || $create ? Input::old('email') : $item->email ),array('placeholder'=>__('campuses.form.email.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('phone', __('campuses.form.phone.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('phone',  ( Input::old('phone') || $create ? Input::old('phone') : $item->phone ),array('placeholder'=>__('campuses.form.phone.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('url', __('campuses.form.url.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('url',  ( Input::old('url') || $create ? Input::old('url') : $item->url ),array('placeholder'=>__('campuses.form.url.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('description', __('campuses.form.description.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::textarea('description',  ( Input::old('description') || $create ? Input::old('description') : $item->description ),array('placeholder'=>__('campuses.form.description.placeholder')))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('identifier', __('campuses.form.identifier.label'), array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('identifier',  ( Input::old('identifier') || $create ? Input::old('identifier') : $item->identifier ),array('placeholder'=>__('campuses.form.identifier.placeholder')))?>
      </div>
    </div>

  </fieldset>

  <?php echo Form::actions('campuses')?>

<?php echo Form::close()?>