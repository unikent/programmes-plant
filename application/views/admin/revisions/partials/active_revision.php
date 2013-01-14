<?php
	$diff_link = action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'.' . $programme->id . '@difference', array($revision->id));
	$live_link = action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'.' . $programme->id . '@make_live', array($revision->id));
	$use_link = action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'.' . $programme->id . '@use_revision', array($revision->id));
	$revert_link = action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'.' . $programme->id . '@revert_to_previous', array($revision->id));
?>
<?php if($revision->status =='live'):?>

	<div style='padding:10px;' class='alert alert-success alert-block'>
		<span class="label label-success" >Published</span>
		<?php echo $revision->get_identifier_string() ?> <br/>
		Published at <?php echo $revision->get_published_time(); ?> by <?php echo $revision->made_live_by; ?>
	</div>

<?php elseif($revision->status =='selected'):?>

		<div style='padding:10px;' class='alert alert-info alert-block'>		
			<div style='float:right;'>
  			<a class="btn btn-info" href="<?php echo $diff_link;?>">Differences from live</a>
  			<a class="popup_toggler btn btn-success" href="#make_revision_live" rel="<?php echo $live_link;?>">Make Live</a>
  			<a class="popup_toggler btn btn-warning" href="#use_previous" rel="<?php echo $revert_link;?>">Use previous</a>
  		</div>
		  <span class="label label-info" >Current revision</span>
		  <?php echo $revision->get_identifier_string() ?><br/>&nbsp;
		</div>

<?php elseif($revision->status == 'unused'):?>

			<div style='padding:5px;margin-left:25px;' class='alert alert-warning'>
				<span class="label label-warning" >unused</span>
				 <?php echo $revision->get_identifier_string() ?> 
				<div style='float:right;'><a class="popup_toggler" href='#use_revision' rel="<?php echo $use_link;?>">Use revision</a> </div>
			</div>

<?php else:?>

			<div style='padding:5px;margin-left:25px;' class='alert alert-info'>
				<span class="label label-info" >R</span> 
				<?php echo $revision->get_identifier_string() ?> 
				<div style='float:right;'><a class="popup_toggler" href='#use_revision' rel="<?php echo $use_link;?>">Use revision</a> | <a class="popup_toggler" href='#make_revision_live' rel="<?php echo $live_link;?>" >Make live</a></div>
			</div>

<?php endif;?>


			