<h2>Revisions</h2>
          <p>The following are revisions of this programme.</p>
          <table class="table table-striped table-bordered table-condensed">
            <thead>
              <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Created By</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($revisions as $revision) : ?>
              <tr>
                <td><?php echo $revision->$title_field ?></td>
                 <td><?php echo $revision->status ?></td>
                <td><?php echo ($revision->created_at); /*echo Date::forge($revision->created_at)->format('%A, %e %B %Y at %l:%M %p');*/ ?></td>
                <td><?php echo $revision->created_by ?></td>
                <td>
                  <a class="popup_toggler btn btn-success" href="#make_revision_live" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $subject->id . '@make_live', array($revision->id)) ?>">Make Live</a>
                  <a class="popup_toggler btn btn-warning" href="#revert" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $subject->id . '@revert_to_revision', array($revision->id)) ?>">Revert To Revision</a>

                  <a class="btn btn-info" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $subject->id . '@difference', array($revision->id)) ?>">Difference</a>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>

          <div class="modal hide fade" id="make_revision_live">
            <div class="modal-header">
              <a class="close" data-dismiss="modal">×</a>
              <h3>Are You Sure?</h3>
            </div>
            <div class="modal-body">
              <p>This will make the current revision live, meaning it will be visable on the course pages.</p>
              <p>Are you sure you want to do this?</p>
            </div>
            <div class="modal-footer">
              <?php echo Form::open('subjects/promote', 'POST')?>
                <a data-dismiss="modal" href="#" class="btn">Not Right Now</a>
                <a class="btn btn-danger yes_action">Make Live</a>
              <?php echo Form::close()?>
            </div>
          </div>

          <div class="modal hide fade" id="revert">
            <div class="modal-header">
              <a class="close" data-dismiss="modal">×</a>
              <h3>Are You Sure?</h3>
            </div>
            <div class="modal-body">
              <p>This will revert the active copy of this page to the selected revision</p>
              <p>Are you sure you want to do this?</p>
            </div>
            <div class="modal-footer">
              <?php echo Form::open('subjects/promote', 'POST')?>
                <a data-dismiss="modal" href="#" class="btn">Not Right Now</a>
                <a class="btn btn-danger yes_action">Revert</a>
              <?php echo Form::close()?>
            </div>
          </div>

       
