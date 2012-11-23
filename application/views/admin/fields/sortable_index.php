<?php echo View::make('admin.inc.scripts')->render()?>

<script type='text/javascript'>

//onLoad setup JS listeners
$(document).ready(function (){

  //Quick/dirty toggle collapse
  $('.toggleCollapse').click(function(a){
    $(this).parent().parent().parent().find('ul').toggle();
  });
   
  // For some reason jquery ui sortable does not provide a method to detect
  // when a field is reorderd ONLY within the current sortable.
  // The below code based on:
  // http://stackoverflow.com/questions/4890923/jquery-ui-sortable-and-two-connected-lists
  // http://jsfiddle.net/39ZvN/9/
  // is needed to do this for us.
  var oldList, newList, item;
  $( "ul.sortable_fields" ).sortable({
    //connect sections together
    connectWith: "ul.sortable_fields",
    //on start keep track of original list.
    start: function(event, ui) {
        item = ui.item; newList = oldList = ui.item.parent().parent();
    },
    //when changed update new list to list item was dragged to
    change: function(event, ui) {  
      if(ui.sender) newList = ui.placeholder.parent().parent();
    },
    //If list has changed trigger onMoved, else trigger onReordered
    stop: function(event, ui) {    
        if(oldList == newList){
          onReorder($(this));
        }else{
           onMoved($(this));
        }
    }
  });
  //Handle reorder within section
  var onReorder = function(sorter){
    var order = sorter.sortable('toArray').toString();
    $.post("<?php echo URL::to('/');?>fields/programmes/reorder", {
       'order': order,
       'section': sorter.parent().attr('id')
    });
  }
  //Handle item moved to new section
  var onMoved = function(sorter){
    var order = newList.find('ul.sortable_fields').sortable('toArray').toString();//sorter.sortable('toArray').toString();
    $.post("<?php echo URL::to('/');?>fields/programmes/reorder", {
       'order': order,
       'section': newList.attr('id')
    });
  }
  //Handle sort for Sections
   $( "div.sortable_sections" ).sortable({
      update: function(event, ui) {
        var order = $(this).sortable('toArray').toString();

        console.log(order);
        // post to our reorder route
       $.post("<?php echo URL::to('/');?>sections/reorder", {
            'order': order
      });
        
      }
    });    
  });
</script>

<div class='sortableUI'>

<h1><?php echo __('fields.title', array('field_name' => __('fields.'.$field_type))); ?></h1>
 <p style="margin-top:20px; margin-bottom:20px"><?php echo  __('fields.introduction.'.$field_type); ?></p>

  <div style="margin-top:20px; margin-bottom:20px">
      <a href="<?php echo url('fields/'.$field_type.'/add')?>" class="btn btn-primary"><?php echo __('fields.btn.new'); ?></a>
        <a href="<?php echo action('/sections@create')?>" class="btn btn-primary">New section</a>
  </div>


  <div class='sortable_sections'>
  <?php if ($sections): ?>
    <?php foreach($sections as $section): ?>

      <div class='section_content' id="section-id-<?php echo $section->id; ?>">
        <legend><?php echo $section->name ?><div style='float:right;'><td> <a class="btn btn-link toggleCollapse" >Show/Hide</a><a class="btn btn-link" href="<?php echo action('/sections@edit', array($section->id)); ?>">Edit</a></div></legend>

         <ul class='sortable_fields'>
               <?php foreach($fields as $field) : ?>
                 <?php if($field->section == $section->id):?>

                   <?php echo View::make('admin.fields.field-row')->with(array('field'=>$field, 'field_type'=>$field_type))->render()?>

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

                <?php echo View::make('admin.fields.field-row')->with(array('field'=>$field, 'field_type'=>$field_type))->render()?>
               <?php endif;?>
             <?php endforeach; ?>
      </ul> 
  </div>   
</div>

   