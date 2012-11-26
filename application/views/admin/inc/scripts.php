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
		$(".multiselect").multiselect({dividerLocation: 0.5});
		
		// sortable items in field listings
		$( ".sortable-tbody" ).sortable({
			update: function(event, ui) {
				// convert the sortable object into a comma-separated list of ids
				var order = $(this).sortable('toArray').toString();
				// post to our reorder route
                $.post("/fields/programmes/reorder", {
                    'order': order
                });
			}
		});
		
		// sortable items in field listings
		$( ".sortable-sections-tbody" ).sortable({
			update: function(event, ui) {
				// convert the sortable object into a comma-separated list of ids
				var order = $(this).sortable('toArray').toString();
				// post to our reorder route
                $.post("/sections/reorder", {
                    'order': order
                });
			}
		});
        
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

    $(function(){
      // Grab all popover elements.
      var els = $('.info [rel=popover]');

      els.each(function(){
        var container = $(this).parent();
        var label = $(container).find('.title').html();
        console.log(label);
        var content = $(container).find('.description').html();

        $(this).popover({ "placement": "top", "title": label, "content": content });
      });
    });
</script>