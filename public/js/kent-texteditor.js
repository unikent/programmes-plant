
var ck_config = {
	toolbar: [
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'BulletedList' ] },
		{ name: 'styles', items: [ 'Format' ] },
		{ name: 'links', items: [ 'Link', 'Unlink', 'Mailto' ] },
		[ 'Scayt'], // spell checker
		[ 'Source'],
		[ 'Undo', 'Redo' ],
		{ name: 'document', items: ['Maximize'] },
	],
	resize_enabled : false,
	customConfig: '',
	language: 'en-gb',
	width: "600px",
	height: "300px",
	format_tags: 'p;h2;h3;h4;h5;h6',
	resize_enabled: true
};

$('textarea').each( function(){
	CKEDITOR.replace( $(this)[0], ck_config);
});