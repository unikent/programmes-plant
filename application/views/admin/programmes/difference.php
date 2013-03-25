<div class="span9 crud">
  <h1>Accept Changes</h2>
  <p>The following shows the differences between the two revisions.</p>
  <table class="table table-striped table-bordered">
    <thead>
      <th></th>
      <th>Current Version saved on <?php echo $revisions['live']->created_at ?></th>
      <th>Revision created on <?php echo $revisions['proposed']->created_at ?></th>
    </thead>

    <tbody>
      <?php foreach($attributes['resolved'] as $key => $value): ?>
        <tr>
          <td><?php echo $attributes['all'][$key]['label']; ?></td>
          <td><?php echo $value['live']; ?></td>
          <td>
            <?php               
              if(!in_array($attributes['all'][$key]['field'], $attributes['nodiff'])):
                echo SimpleDiff::htmlDiff($value['live'], $value['proposed']);
              else:
                echo $value['proposed'];
              endif; 
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="form-actions">
    <a class="btn btn-warning approve_revision_toggler" data-toggle="modal" href="#approve_revision" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@approve_revision')?>">Approve this revision</a>
    <a class="btn btn-danger reject_revision_toggler" data-toggle="modal" href="#reject_revision" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@reject_revision')?>">Reject this revision</a>
  </div>
</div><!-- span9 -->


<div class="modal hide fade" id="approve_revision">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Are You Sure?</h3>
  </div>

  <div class="modal-body">
    <p>This will make the currenty selected revision live, meaning it will be visible on the course pages.</p>
    <p>Are you sure?</p>
  </div>

  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/approve_revision', 'POST')?>
    <?php echo Form::hidden('programme_id', $programme->id); ?>
    <?php echo Form::hidden('revision_id', $revisions['proposed']->id); ?>
    <?php echo Form::submit('Approve', array('class' => 'btn btn-warning')); ?>
    <a data-dismiss="modal" href="#" class="btn">Cancel</a>
    <?php echo Form::close()?>
  </div>
</div>

<div class="modal hide fade" id="reject_revision">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Are You Sure?</h3>
  </div>

  <div class="modal-body">
    <p>This will reject the revision. Are you sure?</p>
  </div>

  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/reject_revision', 'POST')?>
    <?php echo Form::hidden('programme_id', $programme->id); ?>
    <?php echo Form::hidden('revision_id', $revisions['proposed']->id); ?>
    <?php echo Form::submit('Reject', array('class' => 'btn btn-warning')); ?>
    <a data-dismiss="modal" href="#" class="btn">Cancel</a>
    <?php echo Form::close()?>
  </div>
</div>