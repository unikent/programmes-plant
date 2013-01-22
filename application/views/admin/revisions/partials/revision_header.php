<div style='padding:10px;height:30px;' class='alert <?php if($instance->live=='2'):?>alert-success<?php else:?>alert-info<?php endif;?> alert-block'>		
	<div style='float:right;'>
		<?php if($instance->live !='2'):?>
		<a class="popup_toggler btn btn-success" href="#make_revision_live" rel="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/'.$type.'.' . $instance->id . '@make_live', array($revision->id));?>">Make live</a>
		<?php endif;?>
		<a class="btn btn-info" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/'.$type.'@revisions', array($instance->id))?>" ><?php echo __("revisions.manage_revisions"); ?></a>
	</div>

	<?php if($instance->live=='2'):?>
		<span class="label label-success" ><?php echo __("revisions.status_live"); ?></span> 
	<?php else:?>
		<span class="label label-info" ><?php echo __("revisions.status_current"); ?></span> 
	<?php endif;?>
    
    <?php echo $revision->get_identifier_string() ?> 

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