<h1><?php echo Str::title($type); ?></h1>
<p><?php echo __($type . '.introduction'); ?></p>
<?php echo Messages::get_html()?>

<div style="margin-top:20px; margin-bottom:20px">
    <a href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/'. $type . '@create')?>" class="btn btn-primary"><?php echo __($type . '.create') ?></a>
</div>

<?php if ($items) : ?>
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th><?php echo Str::title(Str::singular($type)); ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($items as $item) : ?>
    <tr>
      <td><?php echo $item->name ?></td>
        <td>
          <a class="btn btn-primary" href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/' . $type . '@edit', array($item->id)); ?>"><?php echo __($type . '.edit') ?></a>
           <a href="#remove" class="popup_toggler btn btn-danger" rel="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/'. $type . '@delete', array($item->id)); ?>"><?php echo __($type . '.delete') ?></a>
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

<div class="modal hide fade" id="remove">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __($type . '.modal_header') ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __($type . '.modal_body') ?></p>
  </div>
  <div class="modal-footer">
      <a data-toggle="modal" href="#" class="btn"><?php echo __($type . '.modal_keep') ?></a>
      <a class="btn btn-danger yes_action"><?php echo __($type . '.modal_delete'); ?></a>
  </div>
</div>


