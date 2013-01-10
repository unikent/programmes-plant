<h1><?php echo $programme->{Programme::get_title_field()}; ?><?php echo isset($programme->award->name) ? ' - <em>'.$programme->award->name.'</em>' : '' ; ?></h1>


<style>
  .nav-tabs li {float:right;}
</style>


<ul class="nav nav-tabs nav-tabs-right" >
  <li class="active"><a href="#">Revisions</a></li>
  <li>    <a href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@edit', array($programme->id))?>">Main form</a></li>
</ul>


<h2>Active revisions</h2>

<?php foreach ($revisions as $revision) : ?>

  <?php
    $diff_link = action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $programme->id . '@difference', array($revision->id));
    $live_link = action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $programme->id . '@make_live', array($revision->id));
    $use_link = action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $programme->id . '@use_revision', array($revision->id));
    $revert_link = action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $programme->id . '@revert_to_previous', array($revision->id));
  ?>


  <?php if($revision->status =='live'):?>

    <div style='padding:10px;' class='alert alert-success alert-block'>
     <span class="label label-success" >Published</span>
     <?php echo $revision->created_at;?> by <?php echo $revision->edits_by ?><br/>
     Published at <?php echo $revision->published_at; ?> by <?php echo $revision->made_live_by; ?>
    </div>

    <?php break?>

  <?php elseif($revision->status =='selected'):?>

    <div style='padding:10px;' class='alert alert-info alert-block'>
        <div style='float:right;'>


      <a class="btn btn-info" href="<?php echo $diff_link;?>">Differences from live</a>
      <a class="popup_toggler btn btn-success" href="#make_revision_live" rel="<?php echo $live_link;?>">Make Live</a>
      <a class="popup_toggler btn btn-warning" href="#use_previous" rel="<?php echo $revert_link;?>">Use previous</a>
    </div>
     <span class="label label-info" >Current revision</span>
     <?php echo $revision->created_at;?> by <?php echo $revision->edits_by ?><br/>&nbsp;
    </div>


  <?php else: ?>


    <?php if($revision->status == 'unused'):?>
      <div style='padding:5px;margin-left:25px;' class='alert alert-warning'>
        <span class="label label-warning" >unused</span> <?php echo $revision->created_at;?> From <?php echo $revision->edits_by ?>
        <div style='float:right;'><a class="popup_toggler" href='#use_revision' rel="<?php echo $use_link;?>">Use revision</a> </div>
      </div>
    <?php else:?>
      <div style='padding:5px;margin-left:25px;' class='alert alert-info>'>
        <span class="label label-info" >R</span> <?php echo $revision->created_at;?> From <?php echo $revision->edits_by ?>
        <div style='float:right;'><a class="popup_toggler" href='#use_revision' rel="<?php echo $use_link;?>">Use revision</a> | <a class="popup_toggler" href='#make_revision_live' rel="<?php echo $live_link;?>" >Make live</a></div>
      </div>
    <?php endif;?>

      


  <?php endif; ?>

<?php endforeach; ?>

<p>&nbsp;</p>
<h2>Past revisions</h2>

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


    <div style='padding:5px; <?php if($revision->status != 'prior_live'):?>margin-left:30px;<?php endif;?>' 

      <?php 
      if($revision->status == 'unused'):

        ?>
      class='alert alert-warning alert-block'
    <?php else:?>
      class='alert alert-danger alert-block'
    <?php endif;?>

      >

       <div style='float:right'>
        <a class="popup_toggler<?php if($revision->status == 'prior_live'):?> btn btn-danger<?php endif;?>" href="#make_revision_live" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $programme->id . '@make_live', array($revision->id)) ?>">Roll live back to revision</a> </div>


      <span class="label label-important" >R</span> <?php echo $revision->created_at;?>
      From <?php echo $revision->edits_by ?>
      <?php if($revision->status == 'prior_live'){
        echo "<br/>Was live from ".$revision->published_at;
      } 
      
        ?>


     

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
    <a data-dismiss="modal" href="#" class="btn">Not Right Now</a>
    <a class="btn btn-danger yes_action">Make Live</a>
</div>
</div>

<div class="modal hide fade" id="use_previous">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Are You Sure?</h3>
  </div>
  <div class="modal-body">
    <p>This will revert the active copy of this page to the selected revision</p>
    <p>Are you sure you want to do this?</p>
  </div>
  <div class="modal-footer">
      <a data-dismiss="modal" href="#" class="btn">Not Right Now</a>
      <a class="btn btn-danger yes_action">Revert</a>
  </div>
</div>


<div class="modal hide fade" id="use_revision">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Are You Sure?</h3>
  </div>
  <div class="modal-body">
    <p>This will revert the active copy of this page to the selected revision</p>
    <p>Are you sure you want to do this?</p>
  </div>
  <div class="modal-footer">
      <a data-dismiss="modal" href="#" class="btn">Not Right Now</a>
      <a class="btn btn-danger yes_action">Revert</a>
  </div>
</div>

       
