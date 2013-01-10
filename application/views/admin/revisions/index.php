<h1><?php echo $programme->{Programme::get_title_field()}; ?><?php echo isset($programme->award->name) ? ' - <em>'.$programme->award->name.'</em>' : '' ; ?></h1>

<style>
  .nav-tabs li {float:right;}
</style>

<ul class="nav nav-tabs nav-tabs-right" >
  <li class="active"><a href="#">Revisions</a></li>
  <li>    <a href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@edit', array($programme->id))?>">Main form</a></li>
</ul>

<h2>Active revisions</h2>

<?php
//Loop through revisions (display modes for active & previous are differnt)
$active_r = true;
foreach ($revisions as $revision){
  if($active_r){
      echo View::make('admin.revisions.partials.active_revision', array('revision' => $revision, 'programme' => $programme))->render();
      //After live switch mode to "non-active"
      if($revision->status =='live'){
        $active_r=false;
        echo "<p>&nbsp;</p><h2>Previous revisions</h2>";
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
  <h3>Are You Sure?</h3>
</div>
<div class="modal-body">
  <p>This will make the currenty selected revision live, meaning it will be visable on the course pages.</p>
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
    <p>This will revert the active copy of this page to the previous version</p>
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
    <p>This will set the active copy of this page to the selected revision</p>
    <p>Are you sure you want to do this?</p>
  </div>
  <div class="modal-footer">
      <a data-dismiss="modal" href="#" class="btn">Not Right Now</a>
      <a class="btn btn-danger yes_action">Use revision</a>
  </div>
</div>

       
