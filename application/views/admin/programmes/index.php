<h1><?php echo  URI::segment(1) ?> <?php echo  __('programmes.' . URI::segment(2)) ?> Programmes</h1>
<p style="margin-top:20px; margin-bottom:20px"><?php echo  __('programmes.' . URI::segment(2) . '_introduction', array('year' => URI::segment(1))) ?></p>
<?php echo Messages::get_html()?>

<div style="margin-top:20px; margin-bottom:20px">
  <a href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/programmes@create')?>" class="btn btn-primary"><?php echo __('programmes.create_programme'); ?></a>
</div>
<?php if($programmes) : ?>
    <table id="programme-list" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th><?php echo  __('programmes.table_title') ?></th>
            <th width="250"></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($programmes as $programme) : ?>
          <tr>
            <td><?php echo $programme->$title_field ?><?php echo isset($programme->award->name) ? ' - <em>'.$programme->award->name.'</em>' : '' ; ?></td>
            <td><a class="btn btn-primary" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@edit', array($programme->id))?>"><?php echo  __('programmes.edit_programme') ?></a>
    
              <a class="btn btn-primary" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@create', array($programme->id))?>"><?php echo  __('programmes.clone') ?></a>
    
              <?php if($programme->live == 1): ?>
                <a class="deactivate_toggler btn btn-danger" rel="<?php echo $programme->id ?>">Deactivate</a>
              <?php else: ?>
                <a class="activate_toggler btn btn-success" rel="<?php echo $programme->id ?>">Activate</a>
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
    <h3>Are You Sure?</h3>
  </div>
  <div class="modal-body">
    <p>Are you sure you want to delete this programme?</p>
  </div>
  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/deactivate', 'POST')?>
    <a data-toggle="modal" href="#deactivate_subject" class="btn">Keep</a>
    <input type="hidden" name="id" id="postvalue" value="" />
    <input type="submit" class="btn btn-danger" value="Deactivate" />
    <?php echo Form::close()?>
  </div>
</div>

<div class="modal hide fade" id="activate_subject">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Are You Sure?</h3>
  </div>
  <div class="modal-body">
    <p>Are you sure you want to make the currently selected revision live?</p>
  </div>
  <div class="modal-footer">
    <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/activate', 'POST')?>
    <a data-toggle="modal" href="#activate_subject" class="btn">Keep</a>
    <input type="hidden" name="id" id="postvalue2" value="" />
    <input type="submit" class="btn btn-danger" value="Activate" />
    <?php echo Form::close()?>
  </div>
</div>


 
