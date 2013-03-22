<div class="span9 crud">
  <h1>Accept Changes</h2>
  <p>The following shows the differences between the two revisions.</p>
  <table class="table table-striped table-bordered">
    <thead>
      <th></th>
      <th>Current Version saved on <?php echo $revisions['active']->created_at ?></th>
      <th>Revision created on <?php echo $revisions['proposed']->created_at ?></th>
    </thead>

    <tbody>
      <?php foreach($revisions['proposed']->attributes as $attribute => $value): ?>

        <?php
          $attribute = array(
            'attribute' => $attribute,
            'human' => Revisionable::trim_id_from_field_name($attribute),
          );

          $value = array(
            'active' => is_object($revisions['active']->{$attribute['human']}) ? $revisions['active']->{$attribute['human']}->name : $revisions['active']->{$attribute['human']},
            'proposed' => is_object($revisions['proposed']->{$attribute['human']}) ? $revisions['proposed']->{$attribute['human']}->name : $revisions['proposed']->{$attribute['human']},
          );
        ?>

        <?php if(!in_array($attribute['human'], $attributes['ignore']) && !($value['active'] == $value['proposed'])): ?>
        <tr>
          <td><?php echo (!array_key_exists($attribute['attribute'], $attributes['all'])) ? __('programmes.' . $attribute['attribute']) : $attributes['all'][$attribute['attribute']]; ?></td>
          <td><?php echo $value['active']; ?></td>
          <td>
            <?php               
              if(!in_array($attribute['human'], $attributes['nodiff'])):
                echo SimpleDiff::htmlDiff($value['active'], $value['proposed']);
              else:
                echo $value['proposed'];
              endif; 
            ?>
          </td>
        </tr>
       <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="form-actions">
    <a class="btn btn-danger promote_toggler" href="#promote_revision" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $programme->id . '@promote', array($revisions['proposed']->id))?>">Accept Changes And Promote To Live</a>
    <a class="btn btn-secondary" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/programmes')?>">Ignore For Now</a>
  </div>
</div><!-- span9 -->


<div class="modal hide fade" id="promote_revision">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Are You Sure?</h3>
  </div>
  <div class="modal-body">
    <p>This will promote this revision to the live version of the programme for this year.</p>
    <p>Are you sure you want to do this?</p>
  </div>
  <div class="modal-footer">
    <?php echo Form::open('programmess/promote', 'POST')?>
      <a data-dismiss="modal" href="#promote_revision" class="btn">Not Right Now</a>
      <a class="btn btn-danger" id="promote_now">Promote Revision</a>
  <?php echo Form::close()?>
  </div>
  
  <script>
    $('#promote_revision').modal({
      show:false
    }); // Start the modal

    // Populate the field with the right data for the modal when clicked
    $(".promote_toggler").click(function(){
      $('#promote_now').attr('href', $(this).attr('rel'));
      $('#promote_revision').modal('show');
    });
  </script>
</div>