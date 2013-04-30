<div class="span9 crud">
  <h1><?php echo  __('programmes.diff_header'); ?></h2>
  <p><?php echo  __('programmes.diff_intro'); ?></p>
  <table class="table table-striped table-bordered">
    <thead>
      <th></th>
      <th><?php echo  __('programmes.diff_table_live_header', array('date' => $revisions['live']->created_at)); ?></th>
      <th><?php echo  __('programmes.diff_table_live_header', array('date' => $revisions['proposed']->created_at)); ?></th>
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
    <a class="btn btn-primary" data-toggle="modal" href="#request_changes" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@request_changes')?>"><?php echo  __('programmes.diff_request_amends'); ?></a>
    <a class="btn btn-primary" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@edit', array($programme->id))?>"><?php echo  __('programmes.diff_edit_programme'); ?></a>
    <a class="btn btn-warning approve_revision_toggler" data-toggle="modal" href="#approve_revision" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@approve_revision')?>"><?php echo  __('programmes.diff_approve_revision'); ?></a>
    <a class="btn btn-danger reject_revision_toggler" data-toggle="modal" href="#reject_revision" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@reject_revision')?>"><?php echo  __('programmes.diff_reject_revision'); ?></a>
  </div>
</div><!-- span9 -->

<div class="modal hide fade" id="request_changes">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('programmes.diff_modal.request_changes.header') ?></h3>
  </div>

  <div class="modal-body">
    <?php echo __('programmes.diff_modal.request_changes.body') ?>
  </div>

  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/request_changes', 'POST')?>
    <?php echo Form::hidden('programme_id', $programme->id); ?>
    <?php echo Form::hidden('revision_id', $revisions['proposed']->id); ?>
    <?php echo Form::textarea('message'); ?>
    <?php echo Form::submit(__('programmes.diff_modal.request_changes.submit'), array('class' => 'btn btn-success')); ?>
    <a data-dismiss="modal" href="#" class="btn"><?php echo __('programmes.diff_modal.cancel') ?></a>
    <?php echo Form::close()?>
  </div>
</div>

<div class="modal hide fade" id="approve_revision">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('programmes.diff_modal.approve_revision.header') ?></h3>
  </div>

  <div class="modal-body">
    <?php echo __('programmes.diff_modal.approve_revision.body') ?>
  </div>

  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/approve_revision', 'POST')?>
    <?php echo Form::hidden('programme_id', $programme->id); ?>
    <?php echo Form::hidden('revision_id', $revisions['proposed']->id); ?>
    <?php echo Form::submit(__('programmes.diff_modal.approve_revision.submit'), array('class' => 'btn btn-warning')); ?>
    <a data-dismiss="modal" href="#" class="btn"><?php echo __('programmes.diff_modal.cancel') ?></a>
    <?php echo Form::close()?>
  </div>
</div>

<div class="modal hide fade" id="reject_revision">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('programmes.diff_modal.reject_revision.header') ?></h3>
  </div>

  <div class="modal-body">
    <?php echo __('programmes.diff_modal.reject_revision.body') ?>
  </div>

  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/reject_revision', 'POST')?>
    <?php echo Form::hidden('programme_id', $programme->id); ?>
    <?php echo Form::hidden('revision_id', $revisions['proposed']->id); ?>
    <?php echo Form::submit(__('programmes.diff_modal.reject_revision.submit'), array('class' => 'btn btn-warning')); ?>
    <a data-dismiss="modal" href="#" class="btn"><?php echo __('programmes.diff_modal.cancel') ?></a>
    <?php echo Form::close()?>
  </div>
</div>