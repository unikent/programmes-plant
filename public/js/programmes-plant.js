//methods
function show_options(selectbox) {
    if ($(selectbox).val() == 'select' || $(selectbox).val() == 'checkbox' || $(selectbox).val() == 'table_select' || $(selectbox).val() == 'table_multiselect') {
        //Show all fields
        $('#ext_opt, #form_extra_controls').show();

    }else if($(selectbox).val() == 'help'){
        //Only show top fields.
         $('#ext_opt, #form_extra_controls').hide();
    }else {
      //Show all fields except options
        $('#form_extra_controls').show();
        $('#ext_opt').hide();
    }
}

//onLoad setup JS listeners
$(document).ready(function (){


    //Generic way of creating popups (avoid duplicated code. #value used as id of popup)
    $(".popup_toggler").click(function(){  
      $($(this).attr('href')).find('.yes_action').attr('href', $(this).attr('rel'));
      $($(this).attr('href')).modal('toggle');
    });

    // show options on edit programmes field page
    show_options(document.getElementById('type'));

    // multiselect
    if($('.multiselect')){
        $(".multiselect").multiselect({dividerLocation: 0.5});
    }


    // Grab all popover elements.
    var els = $('.info [rel=popover]');

    els.each(function(){
      var container = $(this).parent();
      var label = $(container).find('.title').html();
      var content = $(container).find('.description').html();

      $(this).popover({ "placement": "top", "title": label, "content": content });
    });


   
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
      $.post(base_url+"ug/fields/programmes/reorder", {
         'order': order,
         'section': sorter.parent().attr('id')
      });
    }
    //Handle item moved to new section
    var onMoved = function(sorter){
      var order_array = newList.find('ul.sortable_fields').sortable('toArray');
      // in case the ordering has an empty list item at the start
      if (order_array[0] == '') order_array.shift();
      var order = order_array.toString();
      $.post(base_url+"ug/fields/programmes/reorder", {
         'order': order,
         'section': newList.attr('id')
      });
    }
    //Handle sort for Sections
    $( "div.sortable_sections" ).sortable({
      update: function(event, ui) {
        var order = $(this).sortable('toArray').toString();
        // post to our reorder route
        $.post(base_url+"ug/sections/reorder", {
            'order': order
        });
        
      }

    });
    
    // toggle collapse
    $('.toggleCollapse').click(function(a){
        $(this).parent().parent().find('ul').slideToggle();
        $(this).find('i').toggleClass('icon-chevron-down');
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
  // invoke the jquery placeholder plugin for IE

  // Invoke the plugin
  //$('input, textarea').placeholder();
  $("[rel=tooltip]").tooltip();


    
    
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

     init_dataTable();
    
    $('.dataTables_filter input').attr("placeholder", "Search programmes").wrap($("<div class='input-prepend'></div>")).parent().prepend($('<span class="add-on"><i class="icon-search"></i></span>'));
    
    /*
    * invoke the jquery placeholder plugin for IE
    */
    $(function() {
      // Invoke the plugin
      $('input, textarea').placeholder();
      $("[rel=tooltip]").tooltip();
    });


    // Word Limit system!
    $('input[data-limit]').each(function(x){

      var self = {};
      // Get word limit from item
      self.limit = $(this).attr("data-limit");
      self.limiting_on_words = false;
      self.dom = document.createElement('span');
      self.dom.className = 'text_limits';

      // Work out if limit should be word or char based
      if(self.limit.substring(self.limit.length-1) == 'w'){
        self.limiting_on_words = true;
        self.limit = parseInt(self.limit.substring(0, self.limit.length-1));
      }else{
        self.limit = parseInt(self.limit);
      }

      // Add display span
      $(self.dom).insertAfter($(this));

      // Find how much of word/char limit is left
      var limit_left = function(field){
        if(self.limiting_on_words){
          //word limits
          //See http://stackoverflow.com/questions/6543917/count-number-of-word-from-given-string-using-javascript
          return self.limit - field.val().split(/\s+\b/).length;
        }else{
          //chars left
          return self.limit - field.val().length;
        }
      }

      // check length of input and show result
      var checkLength = function(field){
        // How much if left
        var left = limit_left(field);

        // If less than 5, display in Red
        if(left < 5){
          self.dom.style.color = 'red';
        }else{
          self.dom.style.color = '';
        }

        //show message to user
        if(self.limiting_on_words){
          self.dom.innerHTML = left+' words left';
        }else{
          self.dom.innerHTML =  left+' characters left';
        }
      }
      //Attach listeners
      $(this).keydown(function(){ checkLength($(this)); });
      $(this).focus(function(){ checkLength($(this)); });
      checkLength($(this)); // so there is data initally.
    });

});

function init_dataTable(){

  try{

    $('#programme-list').dataTable( {
        "sDom": "<'navbar'<'navbar-inner'<'navbar-search pull-left'f>>r>t<'muted pull-right'i><'clearfix'>p",
        "sPaginationType": "bootstrap",
        "iDisplayLength": 20,
        "oLanguage": {
            "sSearch": ""
        },
        "aoColumns": [ 
            { "bSortable": false, 'iDataSort': 1 },
            { "bSortable": false},
            { "bSortable": false }
          ]
    });

  }catch(e){
    // Dont kill the page on error...
  }
}
    

