
      <h1><?php echo ( $create ? 'New Section' : 'Edit Section' )?></h1>
      <?php echo Messages::get_html()?>
      <?php echo Form::open('/'.$type.'/sections/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
        
        <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $section->id ?>" /> <?php endif; ?>
         
        <fieldset>
          <legend>Section Details</legend>
    
          <div class="control-group">
            <?php echo Form::label('name', 'Name', array('class'=>'control-label'))?>
            <div class="controls">
              <?php echo Form::text('name',  ( Input::old('uname') || $create ? Input::old('name') : $section->name ),array('placeholder'=>'Enter Section Name...'))?>
            </div>
          </div>
        </fieldset>
        <div class="form-actions">
          <input type="submit" class="btn btn-warning" value="<?php echo __('sections.save'); ?>" />
          <a class="btn" href="<?php echo url('/'.$type.'/sections')?>"><?php echo __('sections.cancel'); ?></a>
        </div>
    
      <?php echo Form::close()?>

    <div class="modal hide fade" id="delete_section">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are You Sure?</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this section?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open($type.'/section/delete', 'POST')?>
        <a data-dismiss="modal" href="#delete_section" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue" value="" />
        <input type="submit" class="btn btn-danger" value="Delete" />
        <?php echo Form::close()?>
      </div>
    </div>