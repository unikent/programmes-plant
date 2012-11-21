<h1><?php echo Str::title($type); ?></h1>
<p><?php echo __($type . '.introduction'); ?></p>
<?php echo Messages::get_html()?>
<?php if ($items) : ?>
<table class="table table-striped table-bordered table-condensed">
  <thead>
    <tr>
      <th><?php echo Str::title(Str::singular($type)); ?></th>
      <th><?php echo __($type . '.actions_column') ?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($items as $item) : ?>
    <tr>
      <td><?php echo $item->name ?></td>
        <td>
          <a class="btn btn-primary" href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/' . $type . '@edit', array($item->id)); ?>"><?php echo __($type . '.edit') ?></a> 
          <a class="delete_toggler btn btn-danger" rel="<?php echo $item->id; ?>"><?php echo __($type . '.delete') ?></a>
        </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php else : ?>
<div class="well">
  <p><?php echo __($type . '.no_items') ?></p>
</div>
<?php endif; ?>
<div class="form-actions">
  <a href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/'. $type . '@create')?>" class="btn btn-primary right"><?php echo __($type . '.create') ?></a>
</div>

<div class="modal hide fade" id="delete_<?php echo Str::singular($type); ?>">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __($type . '.modal_header') ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __($type . '.modal_body') ?></p>
  </div>
  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/'. $type . '/delete', 'POST')?>
    <a data-toggle="modal" href="#delete_<?php echo Str::singular($type); ?>" class="btn"><?php echo __($type . '.modal_keep') ?></a>
    <input type="hidden" name="id" id="postvalue" value="" />
    <input type="submit" class="btn btn-danger" value="<?php echo __($type . '.modal_delete'); ?>" />
    <?php echo Form::close()?>
  </div>
</div>
<?php echo View::make('admin.inc.scripts')->render()?>
<script>
  $('#delete_<?php echo Str::singular($type); ?>').modal({
    show:false
  }); // Start the modal

  // Populate the field with the right data for the modal when clicked
  $('.delete_toggler').each(function(index,elem) {
      $(elem).click(function(){
        $('#postvalue').attr('value',$(elem).attr('rel'));
        $('#delete_<?php echo Str::singular($type); ?>').modal('show');
      });
  });
</script>
