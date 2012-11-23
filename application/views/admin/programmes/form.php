<h1><?php echo ( $create ? 'New Programme' : 'Edit Programme' )?></h1>
<?php if (! $create) : ?><h2><?php echo  $programmes->$title_field ?> </h2><?php endif; ?>
<?php echo Messages::get_html()?>
<?php echo Form::open_for_files(URI::segment(1).'/'.URI::segment(2).'/programmes/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
<?php if(!$create): ?>
  <?php echo  Form::hidden('programme_id', $programmes->id); ?>
<?php endif; ?>
  <div class="control-group">
    <?php echo Form::label('year', "Programme Year",array('class'=>'control-label'))?>
    <div class="controls">
                <span class="input-xlarge uneditable-input"><?php echo  ( $create ? URI::segment(1) : $programme->year )?></span>
                <?php echo  Form::hidden('year', ( $create ? URI::segment(1) : $programme->year ), array('class'=>'uneditable-input'))?>
    </div>
  </div>
  <?php echo View::make('admin.inc.partials.formFields', array('fields' => $fields, 'subject' => isset($programmes) ? $programmes : null,'create'=>($create && !$clone)))->render(); ?>


<div class="form-actions">
  <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/programmes')?>">Back</a>
  <input type="submit" class="btn btn-primary" value="<?php echo ($create ? __('programmes.create_programme') : __('programmes.save_programme'))?>" />
</div>
<?php if (isset($revisions)) : ?>
  <?php echo View::make('admin.programmes.partials.revisions', array('revisions' => $revisions, 'subject' => $programmes, 'title_field' => $title_field))->render(); ?>
<?php endif; ?>
<?php echo View::make('admin.inc.scripts')->render()?>
<script>
  $(function() {

    $('#promote_revision').modal({
      show:false
    }); // Start the modal

    // Populate the field with the right data for the modal when clicked
    $(".promote_toggler").click(function(){
      $('#promote_now').attr('href', $(this).attr('rel'));
      $('#promote_revision').modal('show');
    });

  });

    </script>
  </body>
</html>
