<!-- javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo asset('js/jquery.js')?>"></script>
<script src="<?php echo asset('js/admin/jquery-ui-1.8.21.custom.min.js')?>"></script>
<script src="<?php echo asset('js/admin/bootstrap.js')?>"></script>
<script src="<?php echo asset('js/admin/wysiwyg.js')?>"></script>
<script src="<?php echo asset('js/admin/wysiwyg-bs.js')?>"></script>
<script src="<?php echo asset('js/admin/ui.multiselect.js')?>"></script>
<script src="<?php echo asset('js/admin/jquery-ui-listbuilder.js')?>"></script>
<link type="text/css" href="<?php echo asset('css/admin/flick/jquery-ui-1.8.21.custom.css')?>" rel="stylesheet" />
<link type="text/css" href="<?php echo asset('css/admin/ui.multiselect.css')?>" rel="stylesheet" />
<link type="text/css" href="<?php echo asset('css/admin/jquery.ui.listbuilder.css')?>" rel="stylesheet" />

<!-- wysiwyg editor -->
<link rel="stylesheet" type="text/css" href="<?php echo asset('lib/css/prettify.css')?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo asset('src/bootstrap-wysihtml5.css')?>"></link>
<script src="<?php echo asset('lib/js/wysihtml5-0.3.0.js')?>"></script>
<script src="<?php echo asset('src/bootstrap-wysihtml5.js')?>"></script>
<script src="<?php echo asset('js/bootstrap-wysihtml5-kent.js')?>"></script>


<script type="text/javascript">
    
    // multiselect
    if($('.multiselect')){
    	$(".multiselect").multiselect({dividerLocation: 0.5});
    }
    $(document).ready(function (){
        
    });



    if($('#content')){
        $('#content').wysihtml5({
        	"font-styles": false, //Font styling, e.g. h1, h2, etc. Default true
        	"emphasis": true, //Italics, bold, etc. Default true
        	"lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
        	"html": false, //Button which allows you to edit the generated HTML. Default false
        	"link": false, //Button to insert a link. Default true
        	"image": false, //Button to insert an image. Default true
    	});
    }

    if($('.editable_text')){
    	$('.editable_text').each(function(index,elem) {
    		$(elem).wysihtml5();
    	});
    }


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
    $.post("<?php echo URL::to('/');?>ug/fields/programmes/reorder", {
       'order': order,
       'section': sorter.parent().attr('id')
    });
  }
  //Handle item moved to new section
  var onMoved = function(sorter){
    var order = newList.find('ul.sortable_fields').sortable('toArray').toString();//sorter.sortable('toArray').toString();
    $.post("<?php echo URL::to('/');?>ug/fields/programmes/reorder", {
       'order': order,
       'section': newList.attr('id')
    });
  }
  //Handle sort for Sections
   $( "div.sortable_sections" ).sortable({
      update: function(event, ui) {
        var order = $(this).sortable('toArray').toString();
        // post to our reorder route
       $.post("<?php echo URL::to('/');?>ug/sections/reorder", {
            'order': order
      });
        
      }
    });    
  });
</script>