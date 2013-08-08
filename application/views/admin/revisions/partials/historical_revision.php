<?php if($revision->status == 'prior_live'):?>

	<div style='padding:5px;' class='alert alert-info alert-block'>
	   
	  	<span class="label label-warning" >Prior Published</span> <?php echo $revision->get_identifier_string() ?>
	 
	    <br/> Was live from <?php echo $revision->get_published_time();?>
	</div>

<?php elseif($revision->status == 'unused'):?>

	<div style='padding:5px;margin-left:50px;' class='alert alert-warning'>
		<span class="label label-info" ><?php echo __("revisions.status_unused"); ?></span>
		 <?php echo $revision->get_identifier_string() ?> 
		
	</div>


<?php else: ?>

<div style='padding:5px;margin-left:50px' class='alert alert-info'>
	<span class="label label-info" ><?php echo __("revisions.status_draft"); ?></span> 
	<?php echo $revision->get_identifier_string() ?> 
	
</div>

<?php endif;?>