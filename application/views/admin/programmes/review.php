
  <h1>Review changes</h2>
  <p>The following shows the differences between the currently live revision and the one being proposed.</p>
  <table class="table table-striped table-bordered">
    <thead>
      <th></th>
      <th>Live: <div><?php echo $diff['revision_1']->get_identifier_string() ?></div> </th>
      <th>Proposed: <div><?php echo $diff['revision_2']->get_identifier_string() ?></div> </th>
    </thead>

    <tbody>

      <?php foreach($diff['attributes'] as $attribute): ?>
        <tr>
          <td><?php echo $attribute['label']; ?></td>


          <td><?php echo $diff['revision_1']->{$attribute['attribute']}; ?></td>
          <td>


            <?php               
              echo  $diff['revision_2']->{$attribute['attribute']};

            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="form-actions">
    <a class="btn btn-primary" data-toggle="modal" href="#request_changes" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@request_changes')?>">
      <?php echo  __('programmes.diff_request_amends'); ?>
    </a>
    <a class="btn btn-primary" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@edit', array($programme->id))?>">
      <?php echo  __('programmes.diff_edit_programme'); ?>
    </a>
    <a class="btn btn-warning approve_revision_toggler" data-toggle="modal" href="#approve_revision" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@approve_revision')?>">
      Approve and push live<?php /*echo  __('programmes.diff_approve_revision');*/ ?> 
    </a>
   
  </div>
</div><!-- span9 -->

<div class="modal hide fade" id="request_changes">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Request changes from author</h3>
  </div>

  <div class="modal-body">
    <p>Use the form below to send a brief message to the author of this revision.</p>
  </div>

  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/request_changes', 'POST')?>
    <?php echo Form::hidden('programme_id', $programme->id); ?>
    <?php echo Form::hidden('revision_id', $diff['revision_2']->id); ?>
    <?php echo Form::textarea('message'); ?>
    <?php echo Form::submit('Send message', array('class' => 'btn btn-success')); ?>
    <a data-dismiss="modal" href="#" class="btn">Cancel</a>
    <?php echo Form::close()?>
  </div>
</div>

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
    <?php echo Form::hidden('revision_id', $diff['revision_2']->id); ?>
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
    <?php echo Form::hidden('revision_id', $diff['revision_2']->id); ?>
    <?php echo Form::submit('Reject', array('class' => 'btn btn-warning')); ?>
    <a data-dismiss="modal" href="#" class="btn">Cancel</a>
    <?php echo Form::close()?>
  </div>
