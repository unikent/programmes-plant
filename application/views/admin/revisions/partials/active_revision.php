<?php
	$diff_link = action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'.' . $programme->id . '@difference', array($revision->id));
	$live_link = action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'.' . $programme->id . '@make_live', array($revision->id));
	$use_link = action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'.' . $programme->id . '@use_revision', array($revision->id));
	$revert_link = action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'.' . $programme->id . '@revert_to_previous', array($revision->id));
	$unpublish_link = action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'.' . $programme->id . '@unpublish', array($revision->id));
?>
<?php if($revision->status =='live'):?>

	<div style='padding:10px;' class='alert alert-success alert-block'>
		<span class="label label-success" ><?php echo __("revisions.status_live"); ?></span>
		<?php echo $revision->get_identifier_string() ?> <br/>
		Published at <?php echo $revision->get_published_time(); ?> by <?php echo $revision->made_live_by; ?>
		<?php if(get_class($programme) == 'Programme'):?>
			<div style='float:right;'><a class="popup_toggler" href='#unpublish' rel="<?php echo $unpublish_link;?>"><?php echo __("revisions.unpublish"); ?></a> </div>
		<?php endif?>
	</div>

<?php elseif($revision->status =='selected' || $revision->status =='under_review'):?>

		<div style='padding:10px;' class='alert alert-info alert-block'>		
			<div style='float:right;'>
  			<a class="btn btn-info" href="<?php echo $diff_link;?>"><?php echo __("revisions.diff_live"); ?></a>
  			<a class="popup_toggler btn btn-success" href="#make_revision_live" rel="<?php echo $live_link;?>"><?php echo __("revisions.make_live"); ?></a>
  			<a class="popup_toggler btn btn-warning" href="#use_previous" rel="<?php echo $revert_link;?>"><?php echo __("revisions.use_previous"); ?></a>
  		</div>
		  <span class="label label-info" ><?php echo ( $revision->status == 'under_review') ? __("revisions.status_review") : __("revisions.status_current"); ?></span>
		  <?php echo $revision->get_identifier_string() ?><br/>&nbsp;
		</div>

<?php elseif($revision->status == 'unused'):?>

			<div style='padding:5px;margin-left:25px;' class='alert alert-warning'>
				<span class="label label-warning" ><?php echo __("revisions.status_unused"); ?></span>
				 <?php echo $revision->get_identifier_string() ?> 
				<div style='float:right;'><a class="popup_toggler" href='#use_revision' rel="<?php echo $use_link;?>"><?php echo __("revisions.use_revision"); ?></a> </div>
			</div>

<?php else:?>

			<div style='padding:5px;margin-left:25px;' class='alert alert-info'>
				<span class="label label-info" ><?php echo __("revisions.status_draft"); ?></span> 
				<?php echo $revision->get_identifier_string() ?> 
				<div style='float:right;'><a class="popup_toggler" href='#use_revision' rel="<?php echo $use_link;?>"><?php echo __("revisions.use_revision"); ?></a> | <a class="popup_toggler" href='#make_revision_live' rel="<?php echo $live_link;?>" ><?php echo __("revisions.make_live"); ?></a></div>
			</div>

<?php endif;?>


			