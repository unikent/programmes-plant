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

	if($('#content')){
		$('#content').wysihtml5();
    }

    if($('.editable_text')){
    	$('.editable_text').each(function(index,elem) {
    		$(elem).wysihtml5();
    	});
    }
</script>