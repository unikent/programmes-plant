<h2>Active revisions</h2>

<?php foreach ($revisions as $revision) : ?>


  <?php if($revision->status =='live'):?>

  <div style='padding:10px;' class='alert alert-success alert-block'>
   <span class="label label-success" >Published</span>
   <?php echo $revision->created_at;?> by <?php echo $revision->edits_by ?><br/>

   Published at <?php echo $revision->published_at; ?> by <?php echo $revision->made_live_by; ?>
  </div>

  <?php break?>



<?php elseif($revision->status =='selected'):?>


  <div style='padding:10px;' class='alert alert-info alert-block'>
   <span class="label label-info" >Current revision</span>
   <?php echo $revision->created_at;?> 


  <div style='float:right'>


 <a class="btn btn-info" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $subject->id . '@difference', array($revision->id)) ?>">Differences from live</a>
  <a class="popup_toggler btn btn-success" href="#make_revision_live" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $subject->id . '@make_live', array($revision->id)) ?>">Make Live</a>
    <a class="popup_toggler btn btn-warning" href="#revert" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $subject->id . '@revert_to_revision', array($revision->id)) ?>">Use previous</a>

  </div><br/>

   From <?php echo $revision->edits_by ?>
  </div>


  <?php else: ?>




    <div style='padding:5px;margin-left:25px;' class='alert alert-info'>
      <span class="label label-info" >R</span> <?php echo $revision->created_at;?> From <?php echo $revision->edits_by ?>
    </div>


  <?php endif; ?>

<?php endforeach; ?>

<p>&nbsp;</p>
<h2>Rollback to past revisions</h2>

<?php 
$displaying= false;
foreach ($revisions as $revision) : 

?>


<?php

  if(!$displaying){

     if($revision->status =='live'){
        $displaying=true;
     }else{
        continue;
     }

  }

?>


    <div style='padding:5px;height:30px; <?php if($revision->status !='prior_live'){echo "margin-left:20px;";} ?>' class='alert alert-danger alert-block'>

       <div style='float:right'><a class="popup_toggler btn btn-danger" href="#make_revision_live" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $subject->id . '@make_live', array($revision->id)) ?>">Roll live back to revision</a> </div>


      <span class="label label-important" >R</span> <?php echo $revision->created_at;?>



      From <?php echo $revision->edits_by ?>


     

    </div>




<?php endforeach; ?>
<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>

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

       
