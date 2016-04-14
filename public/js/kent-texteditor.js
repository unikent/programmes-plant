
var ck_config = {
	toolbar: [
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'BulletedList' ] },
		{ name: 'styles', items: [ 'Format' ] },
		{ name: 'links', items: [ 'Link', 'Unlink', 'Mailto' ] },
		[ 'Scayt'], // spell checker
		['Iframe'],
		[ 'Source'],
		[ 'Undo', 'Redo' ],
		{ name: 'document', items: ['Maximize'] },
	],
	language: 'en-gb',
	width: "600px",
	height: "300px",
	format_tags: 'p;h2;h3;h4;h5;h6',
	resize_enabled: true
};

$('textarea').each(function(){
	//Add ckeditor
	var ckedit = CKEDITOR.replace($(this)[0], ck_config);

    //instanceReady listener, to set editors to readOnly where appropriate.
    ckedit.on('instanceReady', function( evt ){
		var editor = evt.editor;
		var element = editor.element.$;

		if($(element).attr('readonly')){
			editor.setReadOnly(true);
		}
    }, ckedit.element.$);

 	if($(this).attr('data-limit')){
		//Add word limiting logic
		var self = {};
		// Get word limit from item
	    self.limit = $(this).attr("data-limit");
	    self.limiting_on_words = false;
	    self.dom = document.createElement('span');
	    self.dom.className = 'text_limits';

	    //remove html /formatting so length is based on only words
	    self.stripHTML = function(html){
	    	//strip html, new lines & nbsp;'s
			return html.replace( /<[^<|>]+?>/gi,'' ).replace(/(\r\n|\n|\r)/gm,' ').replace(/(&nbsp;)*/g,'');
		}
		//How much of word limit is left
		self.limit_left = function(field){
	        if(self.limiting_on_words){
	          //word limits
	          //See http://stackoverflow.com/questions/6543917/count-number-of-word-from-given-string-using-javascript
	          return self.limit - field.split(/\s+\b/).length;
	        }else{
	          //chars left
	          return self.limit - field.length;
	        }
	    }
	    //check the length
	    self.checkLength = function(field){
	        // How much if left
	        var left = self.limit_left(self.stripHTML(field));

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

	    // Work out if limit should be word or char based
	    if(self.limit.substring(self.limit.length-1) == 'w'){
	        self.limiting_on_words = true;
	        self.limit = parseInt(self.limit.substring(0, self.limit.length-1));
	    }else{
	        self.limit = parseInt(self.limit);
	    }

	    // Add display span
	    $(self.dom).insertAfter($(this));
	    ckedit.on( 'key', function( evt ){ 
	    	self.checkLength(evt.editor.getData());
	    },ckedit.element.$ );
	    ckedit.on( 'focus', function( evt ){ 
	    	self.checkLength(evt.editor.getData());
	    },ckedit.element.$ );

	    self.checkLength(ckedit.getData());

	}

});