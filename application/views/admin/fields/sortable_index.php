<?php echo View::make('admin.inc.scripts')->render()?>


<style>
 
.field_item {
  padding:5px;
  list-style: none;
  cursor:pointer;
}
.field_item i {padding-right:5px;}
.field_item .title {width:60%;   display:inline-block;}
.field_item .type {width:15%;   display:inline-block;}
.field_item .actions {width:20%;   display:inline-block;}

legend { cursor:pointer;margin-top:10px;}

.sortable_fields {
  min-height:30px;
  margin:0px;

}

.sortable_fields.dropzone-large {

  height:120px;
}
</style>
<script>
$(document).ready(function (){

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

    //console.log(order.sortable('toArray').toString());
    $.post("<?php echo URL::to('/');?>fields/programmes/reorder", {
       'order': order,
       'section': sorter.parent().attr('id')
    });

   // console.log("update: reorder");
  }
  //Handle item moved to new section
  var onMoved = function(sorter){

    var order = newList.find('ul.sortable_fields').sortable('toArray').toString();//sorter.sortable('toArray').toString();
  
    $.post("<?php echo URL::to('/');?>fields/programmes/reorder", {
       'order': order,
       'section': newList.attr('id')
    });

    //console.log("update: new item");
    //console.log("Moved to "+newList.attr('id')+ " order is: "+newList.find('ul.sortable_fields').sortable('toArray').toString());
  }



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

    

          <?php /*
          <?php if ($sections): ?>
              <h2><?php echo  __('fields.table_sections_header_name') ?></h2>
              <table class="table table-striped table-bordered table-condensed">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody class="sortable-sections-tbody">
              <?php foreach($sections as $section): ?>
                <tr id="field-id-<?php echo $section->id?>">
                  <td><i class="icon-move"></i> <?php echo $section->name ?></td>
                  <td><a class="btn btn-primary" href="<?php echo action('/sections@edit', array($section->id)) ?>">Edit</a> <a class="delete_toggler btn btn-danger" rel="<?php echo $section->id ?>">Delete</a></td>
                </tr>
              <?php endforeach ?>
              </tbody></table>
           <?php endif ?>
           
           <h2><?php echo  __('fields.table_fields_header_name') ?></h2>
          <table class="table table-striped table-bordered table-condensed" width="100%">
              <thead>
                <tr>
                  <th><?php echo  __('fields.table_header_name') ?></th>
                  <th><?php echo  __('fields.table_header_type') ?></th>
                  <th></th>
                </tr>
              </thead>
              <tbody <?php echo $field_type == 'programmes' ? 'class="sortable-tbody"' : ''; ?>>
                <?php foreach($fields as $subject) : ?>
                <tr id="field-id-<?php echo $subject->id ?>">
                  <td><?php echo $field_type == 'programmes' ? '<i class="icon-move"></i> ' : ''; ?><?php echo $subject->field_name ?></td>
                  <td><?php echo $subject->field_type ?></td>
                  <td>

                    <a class="btn btn-primary" href="<?php echo url('fields/'.$field_type.'/edit/'.$subject->id);?>"><?php echo __('fields.btn.edit'); ?></a>

                    <?php if($subject->active == 1 ): ?>
                      <a class="btn btn-danger" href='<?php echo url('fields/'.$field_type.'/deactivate');?>?id=<?php echo $subject->id;?>'><?php echo __('fields.btn.deactivate'); ?></a>
                    <?php else: ?>
                      <a class="btn btn-success" href='<?php echo url('fields/'.$field_type.'/reactivate');?>?id=<?php echo $subject->id;?>'><?php echo __('fields.btn.reactivate'); ?></a>
                    <?php endif; ?>

                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
          </table>
        </div>

        */ ?>

      </div>

   