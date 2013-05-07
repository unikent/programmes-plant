<div style='padding:10px;height:30px;' class='alert <?php if($programme->live=='2'):?>alert-success<?php else:?>alert-info<?php endif;?> alert-block'>    
  <div style='float:right;'>
    <a class="btn btn-info" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'@edit', array($programme->id))?>" ><?php echo __("revisions.edit_form"); ?></a>
  </div>
</div>

<h1><?php echo $programme->{Programme::get_title_field()}; ?><?php echo isset($programme->award->name) ? ' - <em>'.$programme->award->name.'</em>' : '' ; ?></h1>

<h3><?php echo __("revisions.active_revisions"); ?></h3>

<?php
// Loop through revisions (display modes for active & previous are differnt)

foreach ($revisions as $revision){

    echo View::make('admin.revisions.partials.active_revision', array('revision' => $revision, 'programme' => $programme))->render();
    //After live switch mode to "non-active"
    if($revision->status =='live'){
      break;
    }
}
?>

<a class="btn btn-danger" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/'.URI::segment(3).'@rollback', array($programme->id))?>" ><?php echo __("revisions.rollback_form"); ?></a>

<p>&nbsp;</p>
<h3><?php echo __("revisions.historical_revisions"); ?></h3>
<?php
// Loop through revisions (display modes for active & previous are differnt)

$showing = false;
foreach ($revisions as $revision){

   if($showing) echo View::make('admin.revisions.partials.historical_revision', array('revision' => $revision, 'programme' => $programme))->render();
    //After live switch mode to "non-active"
  if($revision->status =='live'){ $showing = true; }
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


<div class="modal hide fade" id="unpublish">
<div class="modal-header">
  <a class="close" data-dismiss="modal">×</a>
  <h3><?php echo __('modals.confirm_title'); ?></h3>
</div>
<div class="modal-body">
  <p>This will unpublish the currently selected revision, meaning it will be hidden on the course pages.</p>
  <p><?php echo __('modals.confirm_body'); ?></p>
</div>
<div class="modal-footer">
    <a data-dismiss="modal" href="#" class="btn"><?php echo __("revisions.cancel"); ?></a>
    <a class="btn btn-danger yes_action"><?php echo __("revisions.unpublish"); ?></a>
</div>
</div>

<div class="modal hide fade" id="use_previous">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('modals.confirm_title'); ?></h3>
  </div>
  <div class="modal-body">
    <p>This will revert the active copy of this page to the previous version.</p>
    <p><?php echo __('modals.confirm_body'); ?></p>
  </div>
  <div class="modal-footer">
      <a data-dismiss="modal" href="#" class="btn"><?php echo __("revisions.cancel"); ?></a>
      <a class="btn btn-danger yes_action"><?php echo __("revisions.use_previous"); ?></a>
  </div>
</div>

<div class="modal hide fade" id="use_revision">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('modals.confirm_title'); ?></h3>
  </div>
  <div class="modal-body">
    <p>This will set the active copy of this page to the selected revision</p>
    <p><?php echo __('modals.confirm_body'); ?></p>
  </div>
  <div class="modal-footer">
      <a data-dismiss="modal" href="#" class="btn"><?php echo __("revisions.cancel"); ?></a>
      <a class="btn btn-danger yes_action"><?php echo __("revisions.use_revision"); ?></a>
  </div>
</div>

       
