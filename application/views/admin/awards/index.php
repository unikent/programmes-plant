<h1>Awards</h1>

<p>Use the table below to edit the awards available in this system.</p>

<?php echo Messages::get_html()?>

<?php if($awards) : ?>
<table class="table table-striped table-bordered table-condensed">
  <thead>
    <tr>
      <th>Name</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($awards as $award) : ?>
    <tr>
      <td><?php echo $award->name ?></td>
        <td><a class="btn btn-primary" href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/awards@edit', array($award->id)); ?>">Edit</a> <a class="delete_toggler btn btn-danger" rel="<?php echo $award->id; ?>">Delete</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php else : ?>
<div class="well">
  <p>There are no awards in the system yet. Feel free to add one below.</p>
</div>
<?php endif; ?>
<div class="form-actions">
  <a href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/awards@create')?>" class="btn btn-primary right">New Award</a>
</div>

<div class="modal hide fade" id="delete_award">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Are You Sure?</h3>
  </div>
  <div class="modal-body">
    <p>Are you sure you want to delete this award?</p>
  </div>
  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/awards/delete', 'POST')?>
    <a data-toggle="modal" href="#delete_award" class="btn">Keep</a>
    <input type="hidden" name="id" id="postvalue" value="" />
    <input type="submit" class="btn btn-danger" value="Delete" />
    <?php echo Form::close()?>
  </div>
</div>
<?php echo View::make('admin.inc.scripts')->render()?>
<script>
  $('#delete_award').modal({
    show:false
  }); // Start the modal

  // Populate the field with the right data for the modal when clicked
  $('.delete_toggler').each(function(index,elem) {
      $(elem).click(function(){
        $('#postvalue').attr('value',$(elem).attr('rel'));
        $('#delete_award').modal('show');
      });
  });
</script>
