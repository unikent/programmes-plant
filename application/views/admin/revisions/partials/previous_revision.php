<?php
  $live_link = action(URI::segment(1).'/'.URI::segment(2).'/programmes.' . $programme->id . '@make_live', array($revision->id));
?>

<?php if($revision->status == 'prior_live'):?>

	<div style='padding:5px;' class='alert alert-danger alert-block'>
	    <div style='float:right'>
	    	<a class="popup_toggler btn btn-danger" href="#make_revision_live" rel="<?php echo $live_link?>">Roll live back to revision</a> 
	    </div>
	  	<span class="label label-important" >R</span> <?php echo $revision->created_at;?>
	  	From <?php echo $revision->edits_by ?>
	 
	    <br/> Was live from <?php echo $revision->published_at;?>
	</div>

<?php elseif($revision->status == 'unused'):?>

	<div style='padding:5px;margin-left:25px;' class='alert alert-warning alert-block'>
	    <div style='float:right'>
	    	<a class="popup_toggler" href="#make_revision_live" rel="<?php echo $live_link?>">Roll live back to revision</a> 
	    </div>
	  	<span class="label label-warning" >unused</span> <?php echo $revision->created_at;?>
	  	From <?php echo $revision->edits_by ?>
	 
	</div>

<?php else:?>

	<div style='padding:5px;margin-left:25px;' class='alert alert-danger alert-block'>
	    <div style='float:right'>
	    	<a class="popup_toggler" href="#make_revision_live" rel="<?php echo $live_link?>">Roll live back to revision</a> 
	    </div>
	  	<span class="label label-important" >R</span> <?php echo $revision->created_at;?>
	  	From <?php echo $revision->edits_by ?>
	 
	</div>


<?php endif;?>

