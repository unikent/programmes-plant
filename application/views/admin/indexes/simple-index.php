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
          <a class="btn btn-primary" href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/' . $type . '@edit', array($item->id)); ?>"><?php echo __($type . '.edit') ?></a> <a class="delete_toggler btn btn-danger" rel="<?php echo $item->id; ?>"><?php echo __($type . '.delete') ?></a>
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

<div class="modal hide fade" id="delete_single_field">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __($type . '.modal_header') ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __($type . '.modal_body') ?></p>
  </div>
  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/'. $type . '/delete', 'POST')?>
    <a data-toggle="modal" href="#delete_single_field" class="btn"><?php echo __($type . '.modal_keep') ?></a>
    <input type="hidden" name="id" id="postvalue" value="" />
    <input type="submit" class="btn btn-danger" value="<?php echo __($type . '.modal_delete'); ?>" />
    <?php echo Form::close()?>
  </div>
</div>


