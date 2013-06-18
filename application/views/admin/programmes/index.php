<h1><?php echo View::make('admin.inc.partials.type_marker')->render(); ?>Programmes</h1>
<p style="margin-top:20px; margin-bottom:20px"><?php echo  __('programmes.' . URI::segment(2) . '_introduction', array('year' => URI::segment(1))) ?></p>

<?php echo Messages::get_html()?>

<?php 
  $can_create_programmes = Auth::user()->can("create_programmes");
?>

<?php if($can_create_programmes): ?>
<div style="margin-top:20px; margin-bottom:20px">
  <a href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/programmes@create')?>" class="btn btn-primary"><?php echo __('programmes.create_programme'); ?></a>
</div>
<?php endif; ?>
<?php if($programmes) : ?>
    <table id="programme-list" class="table table-striped table-bordered">
        <thead>
        </thead>
        <tbody>
        <?php foreach($programmes as $programme) : ?>
          <tr>
            <td>
                              
                <?php if ( $programme->get_publish_status() === 'new' ): ?>
                    <span class="label label-important" rel="tooltip" data-original-title="<?php echo __('programmes.traffic-lights.new.tooltip') ?>"><?php echo __('programmes.traffic-lights.new.label') ?></span>
                <?php elseif ( $programme->get_publish_status() === 'published' ): ?>
                    <span class="label label-success" rel="tooltip" data-original-title="<?php echo __('programmes.traffic-lights.published.tooltip') ?>"><?php echo __('programmes.traffic-lights.published.label') ?></span>
                <?php elseif ( $programme->get_publish_status() === 'editing' ): ?>
                    <span class="label label-warning" rel="tooltip" data-original-title="<?php echo __('programmes.traffic-lights.editing.tooltip') ?>"><?php echo __('programmes.traffic-lights.editing.label') ?></span>
                <?php endif; ?>


                <?php if ( $programme->attributes["locked_to"] !== '' ): ?>
                  <span class="label label-important" rel="tooltip" data-original-title="">In Draft</span>
                <?php endif; ?>
            </td>
            <td>
                <?php echo $programme->attributes[$title_field]; ?>

                <?php echo isset($programme->relationships["award"]->attributes["name"]) ? ' - <em>'.$programme->relationships["award"]->attributes["name"].'</em>' : '' ; ?>

                <?php if(strcmp($programme->attributes[$withdrawn_field], 'true') == 0): ?>
                  <span class="label label-important"><?php echo __('programmes.withdrawn_field_text')  ?></span>
                <?php endif; ?>
                <?php if(strcmp($programme->attributes[$suspended_field], 'true') == 0): ?>
                  <span class="label label-important"><?php echo __('programmes.suspended_field_text')  ?></span>
                <?php endif; ?>
                <?php if(strcmp($programme->attributes[$subject_to_approval_field], 'true') == 0): ?>
                  <span class="label label-important"><?php echo __('programmes.subject_to_approval_field_text')  ?></span>
                <?php endif; ?>
            </td>
            <td><a class="btn btn-primary" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@edit', array($programme->attributes["id"]))?>"><?php echo  __('programmes.edit_programme') ?></a>
              <?php if($can_create_programmes): ?>
              <a class="btn btn-primary" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@create', array($programme->attributes["id"]))?>"><?php echo  __('programmes.clone') ?></a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
<?php else : ?>
  <div class="well"><?php echo  __('programmes.no_programmes', array('level' => __('programmes.' . URI::segment(2)), 'year' => URI::segment(1))) ?></div>
<?php endif; ?>


<div class="modal hide fade" id="deactivate_subject">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('programmes.index_modal.deactivate_subject.header'); ?></h3>
  </div>
  <div class="modal-body">
    <?php echo __('programmes.index_modal.deactivate_subject.body'); ?>
  </div>
  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/deactivate', 'POST')?>
    <a data-dismiss="modal" href="#deactivate_subject" class="btn"><?php echo __('programmes.index_modal.cancel'); ?></a>
    <input type="hidden" name="id" id="postvalue" value="" />
    <input type="submit" class="btn btn-danger" value="<?php echo __('programmes.index_modal.activate_subject.submit'); ?>" />
    <?php echo Form::close()?>
  </div>
</div>

<div class="modal hide fade" id="activate_subject">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('programmes.index_modal.activate_subject.header'); ?></h3>
  </div>
  <div class="modal-body">
    <?php echo __('programmes.index_modal.activate_subject.body'); ?>
  </div>
  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/activate', 'POST')?>
    <a data-dismiss="modal" href="#activate_subject" class="btn"><?php echo __('programmes.index_modal.cancel'); ?></a>
    <input type="hidden" name="id" id="postvalue2" value="" />
    <input type="submit" class="btn btn-danger" value="<?php echo __('programmes.index_modal.activate_subject.submit'); ?>" />
    <?php echo Form::close()?>
  </div>
</div>


 
