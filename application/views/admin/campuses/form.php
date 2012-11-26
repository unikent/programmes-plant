<h1><?php echo ( $create ? 'New Campus' : 'Edit Campus' )?></h1>
<?php echo Messages::get_html()?>
<?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/campuses/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
  
  <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $item->id?>" /> <?php endif; ?>
   
  <fieldset>
    <legend>Campus Details</legend>

    <div class="control-group">
      <?php echo Form::label('name', 'Name', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('name',  ( Input::old('name') || $create ? Input::old('name') : $item->name ),array('placeholder'=>'Enter Leaflet Name...'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('title', 'Title', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('title',  ( Input::old('title') || $create ? Input::old('title') : $item->title ),array('placeholder'=>'Campus title'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('title', 'Title', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('title',  ( Input::old('title') || $create ? Input::old('title') : $item->title ),array('placeholder'=>'Campus title'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('address_1', 'Address Line 1', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('address_1',  ( Input::old('address_1') || $create ? Input::old('address_1') : $item->address_1 ),array('placeholder'=>'Campus Address Line 2'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('address_2', 'Address Line 2', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('address_2',  ( Input::old('address_2') || $create ? Input::old('address_2') : $item->address_1 ),array('placeholder'=>'Campus Address Line 2'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('address_3', 'Address Line 3', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('address_3',  ( Input::old('address_3') || $create ? Input::old('address_3') : $item->address_1 ),array('placeholder'=>'Campus Address Line 3'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('town', 'Town', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('town',  ( Input::old('town') || $create ? Input::old('town') : $item->address_1 ),array('placeholder'=>'Campus Town'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('postcode', 'Postcode', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('postcode',  ( Input::old('postcode') || $create ? Input::old('postcode') : $item->address_1 ),array('placeholder'=>'Campus Postcode'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('email', 'E-mail', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('email',  ( Input::old('email') || $create ? Input::old('email') : $item->address_1 ),array('placeholder'=>'Campus E-mail'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('phone', 'Telephone', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('phone',  ( Input::old('phone') || $create ? Input::old('phone') : $item->address_1 ),array('placeholder'=>'Campus Telephone'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('url', 'Website URL', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('url',  ( Input::old('url') || $create ? Input::old('url') : $item->address_1 ),array('placeholder'=>'Campus Website URL'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('description', 'Description', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::textarea('description',  ( Input::old('description') || $create ? Input::old('description') : $item->description ),array('placeholder'=>'Description of campus...'))?>
      </div>
    </div>

    <div class="control-group">
      <?php echo Form::label('identifier', 'Identifier', array('class'=>'control-label'))?>
      <div class="controls">
        <?php echo Form::text('identifier',  ( Input::old('identifier') || $create ? Input::old('identifier') : $item->identifier ),array('placeholder'=>'Campus identifier'))?>
      </div>
    </div>

  </fieldset>
  <div class="form-actions">
    <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/leaflets')?>">Go Back</a>
    <input type="submit" class="btn btn-primary" value="<?php echo ($create ? 'Create Create' : 'Save Campus')?>" />
  </div>

<?php echo Form::close()?>
<?php echo View::make('admin.inc.scripts')->render()?>
