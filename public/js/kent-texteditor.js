
 var ck_config = {
	    toolbar: [
			    	{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'RemoveFormat' ] },
			    	{ name: 'styles', items: [ 'Format' ] },
					{ name: 'links', items: [ 'Link', 'Unlink', 'Mailto' ] },
					[ 'Scayt'], //spell checker?
					[ 'Source'],
					[ 'Undo', 'Redo' ],	
					{ name: 'document', items: ['Maximize'] },																	
				],
			removePlugins : 'elementspath',
			resize_enabled : false,
		    customConfig: '',
			language: 'en-gb'
		};

 $('textarea').each( function(){
 
	CKEDITOR.replace( $(this)[0], ck_config);
});