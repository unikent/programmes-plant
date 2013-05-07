<div style='padding:10px;height:30px;' class='alert <?php if($programme->live=='2'):?>alert-success<?php else:?>alert-info<?php endif;?> alert-block'>    
  <div style='float:right;'>
    <a class="btn btn-warning" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'@revisions', array($programme->id))?>" ><?php echo __("revisions.revision_form"); ?></a>
    <a class="btn btn-info" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'@edit', array($programme->id))?>" ><?php echo __("revisions.edit_form"); ?></a>
  </div>
</div>

<h1><?php echo $programme->{Programme::get_title_field()}; ?><?php echo isset($programme->award->name) ? ' - <em>'.$programme->award->name.'</em>' : '' ; ?></h1>

<h3><?php echo __("revisions.rollback_revisions"); ?></h3>
<p><?php echo __("revisions.rollback_warning"); ?></p>

<?php
// Loop through revisions (display modes for active & previous are differnt)
$active_r = true;
foreach ($revisions as $revision){
  if($active_r){
      //After live switch mode to "non-active"
      if($revision->status =='live'){
        $active_r=false;
      }
  }else{
     echo View::make('admin.revisions.partials.previous_revision', array('revision' => $revision, 'programme' => $programme))->render();
  }
}
?>

<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>

<div class="modal hide fade" id="make_revision_live">
<div class="modal-header">
  <a class="close" data-dismiss="modal">×</a>
  <h3><?php echo __('modals.confirm_title'); ?></h3>
</div>
<div class="modal-body">
  <p>This will make the currently selected revision live, meaning it will be visible on the course pages.</p>
  <p><?php echo __('modals.confirm_body'); ?></p>
</div>
<div class="modal-footer">
    <a data-dismiss="modal" href="#" class="btn"><?php echo __("revisions.cancel"); ?></a>
    <a class="btn btn-danger yes_action"><?php echo __("revisions.make_live"); ?></a>
</div>
</div>

       
