<script type='text/javascript'>
  var programme_sort_type = '<?php echo URLParams::get_type(); ?>';
</script>


<div class='sortableUI'>

<h1><?php echo __('fields.title', array('field_name' => __('fields.programmes'))); ?></h1>
 <p style="margin-top:20px; margin-bottom:20px"><?php echo  __('fields.introduction.programmes'); ?></p>

  <div style="margin-top:20px; margin-bottom:20px">
      <a href="<?php echo url($path.'/add')?>" class="btn btn-primary"><?php echo __('fields.btn.new'); ?></a>
        <a href="<?php echo action($path.'/../sections@create')?>" class="btn btn-primary"><?php echo __('fields.btn.new-section') ?></a>
  </div>




  <div class='sortable_sections'>
  <?php if ($sections): ?>
    <?php foreach($sections as $section): ?>

        <div class="section_content" id="section-id-<?php echo $section->id; ?>">
        
            <div class="lead section_name">

              <?php echo $section->name ?>
              <a class="btn btn-link toggleCollapse right" ><i class="icon-chevron-up"></i></a><a class="btn btn-link" href="<?php echo action($path.'/sections@edit', array($section->id)); ?>">Edit</a>
            </div>
            
            <ul class='sortable_fields'>
               <?php foreach($fields as $field) : ?>
                 <?php if($field->section == $section->id):?>
                   <?php echo View::make('admin.fields.field-row')->with(array('field'=>$field, 'path'=>$path))->render();?>            
                 <?php endif;?>
               <?php endforeach; ?>
            </ul>
        </div>
     <?php endforeach ?>
   <?php endif ?>
 </div>

 <div class="section_content" id="section-id-0">
    <div class="lead section_name_inactive">Inactive fields<a class="btn btn-link toggleCollapse right" ><i class="icon-chevron-up"></i></a></div>
        <ul class='sortable_fields'>
            <?php foreach($fields as $field) : ?>
               <?php if($field->section == 0):?>

                <?php echo View::make('admin.fields.field-row')->with(array('field'=>$field, 'path'=>$path))->render()?>
               <?php endif;?>
             <?php endforeach; ?>
        </ul> 
  </div>   
</div>


   