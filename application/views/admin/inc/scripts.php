<!-- javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo asset('js/jquery.js')?>"></script>
<script src="<?php echo asset('js/admin/jquery-ui-1.8.21.custom.min.js')?>"></script>
<script src="<?php echo asset('js/admin/bootstrap.js')?>"></script>
<!-- wysihtml5 js -->
<script src="<?php echo asset('lib/js/wysihtml5-0.3.0.js')?>"></script>
<script src="<?php echo asset('src/bootstrap-wysihtml5.js')?>"></script>
<script src="<?php echo asset('js/bootstrap-wysihtml5-kent.js')?>"></script>
<!-- multiselect js -->
<script src="<?php echo asset('js/admin/ui.multiselect.js')?>"></script>
<script src="<?php echo asset('js/admin/jquery-ui-listbuilder.js')?>"></script>
<!-- placeholder for ie fix js -->
<script src="<?php echo asset('js/admin/jquery.placeholder.min.js')?>"></script>
<!-- data tables js -->
<script type="text/javascript" charset="utf8" src="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.1/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf-8" language="javascript" src="<?php echo asset('js/admin/DT_bootstrap.js')?>"></script>

<script type="text/javascript">
    
    // multiselect
    if($('.multiselect')){
        $(".multiselect").multiselect({dividerLocation: 0.5});
    }
    
    /**
    *
    * wysihtml5 text editor
    *
    */
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
   
   /**
   *
   * programme fields and section reordering
   *
   */
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
        },
    });
    
    //Handle reorder within section
    var onReorder = function(sorter){
    var order = sorter.sortable('toArray').toString();
    $.post("<?php echo URL::to('/');?>ug/fields/programmes/reorder", {
       'order': order,
       'section': sorter.parent().attr('id')
    });
    console.log(sorter.parent().attr('id'));
    }
    //Handle item moved to new section
    var onMoved = function(sorter){
    var order_array = newList.find('ul.sortable_fields').sortable('toArray');
    // in case the ordering has an empty list item at the start
    if (order_array[0] == '') order_array.shift();
    var order = order_array.toString();
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
    
    // toggle collapse
    $('.toggleCollapse').click(function(a){
        $(this).parent().parent().find('ul').slideToggle();
        $(this).find('i').toggleClass('icon-chevron-down');
    });

    //Generic way of creating popups (avoid duplicated code. #value used as id of popup)
    $(".popup_toggler").click(function(){
      console.log($($(this).attr('href')).find('.yes_action'));
      $($(this).attr('href')).find('.yes_action').attr('href', $(this).attr('rel'));
      $($(this).attr('href')).modal('show');
    });
    
    
    /**
    *
    * promote revision modal
    *
    */
    $(function() {
    
        $('#promote_revision').modal({
          show:false
        }); // Start the modal
        
        // Populate the field with the right data for the modal when clicked
        $(".promote_toggler").click(function(){
          $('#promote_now').attr('href', $(this).attr('rel'));
          $('#promote_revision').modal('show');
        });
    
    });
    
    
    /**
    *
    * delete section modal
    *
    */
    $('#delete_section').modal({
     show:false
    }); // Start the modal
    
    // Populate the field with the right data for the modal when clicked
    $('.delete_toggler').each(function(index,elem) {
      $(elem).click(function(){
        $('#postvalue').attr('value',$(elem).attr('rel'));
        $('#delete_section').modal('show');
      });
    });
    
    
    /**
    *
    * modals for activating and deactivating programmes
    *
    */
    $('#delete_single_field').modal({
    show:false
    }); // Start the modal
    
    // Populate the field with the right data for the modal when clicked
    $('.delete_toggler').each(function(index,elem) {
      $(elem).click(function(){
        $('#postvalue').attr('value',$(elem).attr('rel'));
        $('#delete_single_field').modal('show');
      });
    });
    
    
    $('#deactivate_subject').modal({
    show:false
    }); // Start the modal
    
    // Populate the field with the right data for the modal when clicked
    $('.deactivate_toggler').each(function(index,elem) {
      $(elem).click(function(){
        $('#postvalue').attr('value',$(elem).attr('rel'));
        $('#deactivate_subject').modal('show');
      });
    });
    
    $('#activate_subject').modal({
    show:false
    }); // Start the modal
    
    // Populate the field with the right data for the modal when clicked
    $('.activate_toggler').each(function(index,elem) {
      $(elem).click(function(){
        $('#postvalue2').attr('value',$(elem).attr('rel'));
        $('#activate_subject').modal('show');
      });
    });
    
    /**
    *
    * data tables for programme index page
    *
    */
    $('#programme-list').dataTable( {
        "sDom": "<'navbar'<'navbar-inner'<'navbar-search pull-left'f>>r>t<'muted pull-right'i><'clearfix'>p",
        "sPaginationType": "bootstrap",
        "iDisplayLength": 20,
        "oLanguage": {
            "sSearch": ""
        },
        "aoColumns": [ 
          { "bSortable": false },
          { "bSortable": false },
          { "bSortable": false },
          { "bSortable": false },
          ]
    });
    $('.dataTables_filter input').attr("placeholder", "Search programmes").wrap($("<div class='input-prepend'></div>")).parent().prepend($('<span class="add-on"><i class="icon-search"></i></span>'));
    
    /*
    * invoke the jquery placeholder plugin for IE
    */
    $(function() {
    // Invoke the plugin
    $('input, textarea').placeholder();
    $("[rel=tooltip]").tooltip();
    });

</script>