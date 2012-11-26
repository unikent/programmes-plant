
<div class='sortableUI'>

<h1><?php echo __('fields.title', array('field_name' => __('fields.'.$field_type))); ?></h1>
 <p style="margin-top:20px; margin-bottom:20px"><?php echo  __('fields.introduction.'.$field_type); ?></p>

  <div style="margin-top:20px; margin-bottom:20px">
      <a href="<?php echo url($type.'/fields/'.$field_type.'/add')?>" class="btn btn-primary"><?php echo __('fields.btn.new'); ?></a>
        <a href="<?php echo action('/'.$type.'/sections@create')?>" class="btn btn-primary">New section</a>
  </div>




  <div class='sortable_sections'>
  <?php if ($sections): ?>
    <?php foreach($sections as $section): ?>

      <div class='section_content' id="section-id-<?php echo $section->id; ?>">
        <legend><?php echo $section->name ?><div style='float:right;'><td> <a class="btn btn-link toggleCollapse" >Show/Hide</a><a class="btn btn-link" href="<?php echo action('/'.$type.'/sections@edit', array($section->id)); ?>">Edit</a></div></legend>

         <ul class='sortable_fields'>
               <?php foreach($fields as $field) : ?>
                 <?php if($field->section == $section->id):?>

                   <?php echo View::make('admin.fields.field-row')->with(array('field'=>$field, 'field_type'=>$field_type, 'type'=>$type))->render()?>

                 <?php endif;?>
               <?php endforeach; ?>
         </ul> 
        </div>
     <?php endforeach ?>
   <?php endif ?>
 </div>

 <div id="section-id-0">
   <legend style='color:red'>Inactive</legend>
    <ul class='sortable_fields dropzone-large'>
            <?php foreach($fields as $field) : ?>
               <?php if($field->section == 0):?>

                <?php echo View::make('admin.fields.field-row')->with(array('field'=>$field, 'field_type'=>$field_type, 'type'=>$type))->render()?>
               <?php endif;?>
             <?php endforeach; ?>
      </ul> 
  </div>   
</div>

<?php echo View::make('admin.inc.scripts')->render()?>
   