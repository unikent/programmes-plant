<?php if(isset($instance->locked_to) && $instance->locked_to !== '' && $revision !== null):?>

	<div style='padding:10px;' class='alert alert-warning alert-block'>		
		<span class="label label-warning pull-right"><?php echo __('revisions.draft'); ?></span>
		<h4><?php echo __('revisions.draft_warning'); ?></h4>
		<p>&nbsp;</p>

		<?php if ($revision->under_review == 1) : ?>
			<p><?php echo __('revisions.under_review_warning'); ?></p>
		<?php else: ?>
			<p><?php echo __('revisions.review_warning'); ?></p>
		<?php endif; ?>
		<p>&nbsp;</p>
	</div>
<?php endif; ?>

<?php
 	$revisions_link = action(URLParams::get_variable_path_prefix().$type.'@revisions', array($instance->id));
?>


<?php if($revision === null): ?>
	<div style='padding:10px;height:30px;' class='alert alert-danger'>
		<div style='float:right;'>
			<?php if (Auth::user()->can('manage_revisions')) : ?>
				<a class="btn btn-info" href="<?php echo $revisions_link; ?>" ><?php echo __("revisions.manage_revisions"); ?></a>
			<?php endif;?>
		</div>
		<span class="label label-important">Unknown</span> Publishing state unknown.
	</div>
<?php else: ?>
<div style='padding:10px;height:30px;' class='alert <?php if($instance->get_publish_status() === 'published'):?>alert-success<?php else:?>alert-info<?php endif;?> alert-block'>		
	<div style='float:right;'>

		<?php if($type == 'programmes'):?>
			<?php
				$preview_link =  action(URLParams::get_variable_path_prefix().'programmes/'.$instance->id.'@preview', array($revision->id));
				$review_link = action( URLParams::get_variable_path_prefix() . 'programmes/' . $instance->id . '@review', array($revision->id));
			?>
			<a class="btn btn-warning" target="_blank" href="<?php echo $preview_link; ?>" ><?php echo __("revisions.view_preview"); ?></a>
		<?php endif; ?>

		<?php if($instance->get_publish_status() === 'editing' || $instance->get_publish_status() === 'new'):?>
			<?php if (Auth::user()->can('make_programme_live')) : ?>
				<?php if($instance->locked_to == ''):?>
					<a class="popup_toggler btn btn-success" href="#make_revision_live" rel="<?php echo action(URLParams::get_variable_path_prefix().$type.'.' . $instance->id . '@make_live', array($revision->id));?>"><?php echo __("revisions.make_live"); ?></a>
				<?php else:?>
					<a class="btn btn-success" target="_blank" href="<?php echo $review_link; ?>" ><?php echo __("revisions.review"); ?></a>
				<?php endif;?>
			<?php else : ?>
				<a class="popup_toggler btn btn-success" href="#send_for_editing" rel="<?php echo action(URLParams::get_variable_path_prefix().$type.'.' . $instance->id . '@submit_programme_for_editing', array($revision->id));?>"><?php echo __('revisions.send_for_editing'); ?></a>
			<?php endif; ?>
		<?php endif;?>

		<?php if (Auth::user()->can('manage_revisions')) : ?>
		<a class="btn btn-info" href="<?php echo $revisions_link; ?>" ><?php echo __("revisions.manage_revisions"); ?></a>
		<?php endif; ?>

	</div>
    <?php if (Auth::user()->can('make_programme_live')) : ?>
    	<?php if ($instance->get_publish_status() === 'published'):?>
    		<span class="label label-success" ><?php echo __("revisions.status_live"); ?></span> 
    	<?php elseif ($revision->under_review == 1) : ?>
    		<span class="label label-important"><?php echo __("revisions.status_review"); ?></span>
    	<?php else : ?>
    		<span class="label label-info" ><?php echo __("revisions.status_current"); ?></span> 
    	<?php endif;?>
        <?php echo $revision->get_identifier_string() ?>
    <?php endif; ?>

</div>


<div class="modal hide fade" id="make_revision_live">
	<div class="modal-header">
	  <a class="close" data-dismiss="modal">×</a>
	  <h3><?php echo __('modals.confirm_title'); ?></h3>
	</div>
	<div class="modal-body">
	  <p><?php echo __('revisions.modals.live_warning'); ?></p>
	  <p><?php echo __('modals.confirm_body'); ?></p>
	</div>
	<div class="modal-footer">
	    <a data-dismiss="modal" href="#" class="btn"><?php echo __("revisions.cancel"); ?></a>
   		<a class="btn btn-danger yes_action"><?php echo __("revisions.make_live"); ?></a>
	</div>
</div>
<div class="modal hide fade" id="send_for_editing">
	<div class="modal-header">
	  <a class="close" data-dismiss="modal">×</a>
	  <h3><?php echo __('modals.confirm_title'); ?></h3>
	</div>
	<div class="modal-body">

	<?php if ($revision->under_review == 1) : ?>
		<?php echo __('revisions.modals.under_review_warning'); ?>
	<?php else : ?>
	  <p><?php echo __('revisions.modals.review_warning'); ?></p>
	  <p><?php echo __('modals.confirm_body'); ?></p>
	<?php endif; ?>
	</div>
	<div class="modal-footer">
	    <a data-dismiss="modal" href="#" class="btn"><?php echo __('revisions.cancel'); ?></a>
   		<a class="btn btn-danger yes_action"><?php echo __('revisions.send_for_editing'); ?></a>
	</div>
</div>

<?php endif; ?>
