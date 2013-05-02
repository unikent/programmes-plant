<?php if($instance->locked_to !== ''):?>

<div style='padding:10px;' class='alert alert-warning alert-block'>		
	<div style='float:right;'>
			
			<!--a class="btn btn-success" target="_blank" href="" >Review changes</a-->
	</div>		
	<span class="label label-warning pull-right">Draft</span>
	<h4>Warning: This programme contains changes that are not yet ready to go live.</h4>
	<p>&nbsp;</p>

	<?php if ($revision->under_review == 1) : ?>
		<p>Changes have been sent to EMS for publishing.</p>
	<?php else: ?>
		<p>The latest changes to this programme have not yet been sent to EMS for publishing.</p>
	<?php endif; ?>
	<p>&nbsp;</p>
</div>
<?php endif; ?>

<div style='padding:10px;height:30px;' class='alert <?php if($instance->live=='2'):?>alert-success<?php else:?>alert-info<?php endif;?> alert-block'>		
	<div style='float:right;'>
		<?php if($type == 'programmes'):?>
			<?php
				$preview_link =  action(URI::segment(1).'/'.URI::segment(2).'/programmes/'.$instance->id.'@preview', array($revision->id));
			?>
			<a class="btn btn-warning" target="_blank" href="<?php echo $preview_link; ?>" ><?php echo __("revisions.view_preview"); ?></a>
		<?php endif; ?>

		<?php if($instance->live != '2'):?>

		<?php if (Auth::user()->can('make_programme_live')) : ?>
			<?php if($instance->locked_to == ''):?>
				<a class="popup_toggler btn btn-success" href="#make_revision_live" rel="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/'.$type.'.' . $instance->id . '@make_live', array($revision->id));?>">Make live</a>
			<?php endif;?>
		<?php else : ?>
			<a class="popup_toggler btn btn-success" href="#send_for_editing" rel="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/'.$type.'.' . $instance->id . '@submit_programme_for_editing', array($revision->id));?>">Send for editing</a>
		<?php endif; ?>

		<?php endif;?>

		<?php if (Auth::user()->can('manage_revisions')) : ?>
		<a class="btn btn-info" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/'.$type.'@revisions', array($instance->id))?>" ><?php echo __("revisions.manage_revisions"); ?></a>
		<?php endif; ?>

	</div>
    <?php if (Auth::user()->can('make_programme_live')) : ?>
    	<?php if ($instance->live=='2'):?>
    		<span class="label label-success" ><?php echo __("revisions.status_live"); ?></span> 
    	<?php elseif ($revision->status == 'under_review') : ?>
    		<span class="label label-important">Under review</span>
    
    	<?php else : ?>
    		<span class="label label-info" ><?php echo __("revisions.status_current"); ?></span> 
    	<?php endif;?>
        
        <?php echo $revision->get_identifier_string() ?>
    <?php endif; ?>

</div>


<div class="modal hide fade" id="make_revision_live">
	<div class="modal-header">
	  <a class="close" data-dismiss="modal">×</a>
	  <h3>Are You Sure?</h3>
	</div>
	<div class="modal-body">
	  <p>This will make the currenty selected revision live, meaning it will be visable on the course pages.</p>
	  <p>Are you sure you want to do this?</p>
	</div>
	<div class="modal-footer">
	    <a data-dismiss="modal" href="#" class="btn"><?php echo __("revisions.cancel"); ?></a>
   		<a class="btn btn-danger yes_action"><?php echo __("revisions.make_live"); ?></a>
	</div>
</div>
<div class="modal hide fade" id="send_for_editing">
	<div class="modal-header">
	  <a class="close" data-dismiss="modal">×</a>
	  <h3>Are You Sure?</h3>
	</div>
	<div class="modal-body">
	<?php if ($revision->status == 'under_review') : ?>
	<p><strong>You have already made edits to this and sent them for review by EMS</strong></p>
	<p>Are you sure you want to send it again?</p>
	<?php else : ?>
	  <p>This means that you have completed edits and that you are sending this version of programme to EMS for proofing and editing.</p>
	  <p>Are you sure you want to do this?</p>
	<?php endif; ?>
	</div>
	<div class="modal-footer">
	    <a data-dismiss="modal" href="#" class="btn">Not right now</a>
   		<a class="btn btn-danger yes_action">Send for editing</a>
	</div>
</div>