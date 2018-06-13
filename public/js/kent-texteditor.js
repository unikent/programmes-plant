
/*
TODO

- something with the image uploads
- is there a better way to do length limits?

*/

/* 

TODO image stuff
$R.prototype.imagemanager = function()
{
	return {
		langs: {
			en: {
				"upload": "Upload",
				"choose": "Choose"
			}
		},
		show: function()
		{
			// build modal
			this.modal.load('image', this.lang.get('image'), 700);

			// build upload
			this.upload.init('#redactor-modal-image-droparea', this.opts.imageUpload, !this.opts.picker ? this.imagemanager.insert : this.imagemanager.pick);
			this.modal.show();

		},
		buildAttribution: function(json){
			$attribution = '<div class="attribution"><i class="kf-camera"></i><span class="attribution-text">';
			if(typeof json.title !== 'undefined' && json.title.length > 0) {
				$attribution += json.title +' : ';
			}
			if(typeof json.attribution.author !== 'undefined' && json.attribution.author.length > 0 ) {
				$attribution += 'Picture by ';
				if(typeof json.attribution.link !== 'undefined' && json.attribution.link.length > 0 ) {
					$attribution += '<a href="' + json.attribution.link + '">';
				}
				$attribution += json.attribution.author;
				if(typeof json.attribution.link !== 'undefined' && json.attribution.link.length > 0 ) {
					$attribution += '</a>';
				}
				$attribution += '.';
			}
			if(typeof json.attribution.license !== 'undefined' && json.attribution.license.length > 0 ) {
				$attribution += ' <a href="' + json.attribution.license + '">Licence</a>';
			}
			$attribution += '</span></div>';
			return $attribution;
		},
		getAttributionFields :function(readonly){
			$html = '<section><label>Attribution Text</label><input type="text" id="redactor-image-attribution-text"' + (readonly ? ' readonly disabled':'') + '></section>';
			$html += '<section><label>Attribution Link</label><input type="text" id="redactor-image-attribution-link"' + (readonly ? ' readonly disabled':'') + '></section>';
			$html += '<section><label>Licence Link</label><input type="text" id="redactor-image-licence-link"' + (readonly ? ' readonly disabled':'') + '></section>';
			return $($html);
		},
		init: function()
			   {
				   if (!this.opts.imageManagerJson)
				   {
					   return;
				   }
				   this.modal.addCallback('image', this.imagemanager.load);

				   this.image.insert = !this.opts.picker ? this.imagemanager.insert : this.imagemanager.pick;
				   this.image.showEdit = this.imagemanager.showEdit;
				   this.image.update = this.imagemanager.update;
				   this.image.setFloating = this.imagemanager.setFloating;
				   this.image.show = this.imagemanager.show;
			   },
		load: function()
			   {
				   var $box = $('<div style="display: none;" class="redactor-modal-tab" data-title="Choose"><div><input id="img-gallery-search" placeholder="search gallery" type="text"></div><div id="img-gallery"></div></div>');
				   this.modal.getModal().append($box);
				   var $gallery = $box.find('#img-gallery');
				   var search  = $box.find('#img-gallery-search');
				   search.keyup(function(e){
					   var val = $(e.target).val().toLowerCase();
					   $gallery.children().each(function(i, e){
						   var el = $(e);
						   if(el.find('.img-name').text().toLowerCase().indexOf(val) < 0){
							   el.fadeOut();
						   }else{
							   el.fadeIn()
						   }
					   });
				   });
				   $.ajax({
					   dataType: "json",
					   cache: false,
					   url: this.opts.imageManagerJson,
					   success: $.proxy(function(data)
					   {
						   $.each(data, $.proxy(function(key, val)
						   {
							   // title
							   var thumbtitle = '';
							   if (typeof val.name !== 'undefined')
							   {
								   thumbtitle = val.name;
							   }

							   var img = $('<div class="gallery-img-wrap"><img src="' + val.sizes.thumbnail.url + '"  alt="' + thumbtitle + '" style="width:160px; height: auto; cursor: pointer;" /><div class="img-name"><span>' + val.name + '</span></div></div>');
							   img.data('params',val);
							   $gallery.append(img);
							   $(img).click($.proxy(this.imagemanager.insertFromGallery, this));

						   }, this));

					   }, this)
				   });


			   },
		insertFromGallery: function(e)
			   {
				   var $el = $(e.currentTarget);
				   var json = $el.data('params');

				   this.image.insert(json, null);
			   },
		pick: function(json, direct, e){

			// error callback
			if (typeof json.error !== 'undefined')
			{
				this.modal.close();
				this.events.dropImage = false;
				this.core.callback('imageUploadError', json, e);
				return;
			}

			this.placeholder.hide();

			this.modal.close();

			// buffer
			this.buffer.set();

			// insert
			this.air.collapsed();

			var picker = this.$element.closest('.img-picker');
			picker.find('.img-name').html(json.name);
			picker.find('.img-wrapper').html('<img src="' + json.sizes.thumbnail.url + '" alt="' + json.alt_text + '">');

			this.code.set(json.id);
			this.code.sync();

			this.events.dropImage = false;

		},
		insert: function(json, direct, e)
				{
					var $img;

					// error callback
					if (typeof json.error !== 'undefined')
					{
						this.modal.close();
						this.events.dropImage = false;
						this.core.callback('imageUploadError', json, e);
						return;
					}

					// change image
					if (this.events.dropImage !== false)
					{
						$img = $(this.events.dropImage);

						this.core.callback('imageDelete', $img[0].src, $img);

						$img.attr('src', json.sizes.full.url);

						var id = (typeof json.id === 'undefined') ? '' : json.id;
						var type = (typeof json.s3 === 'undefined') ? 'image' : 's3';
						$img.attr('data-' + type, id);

						this.events.dropImage = false;
						this.core.callback('imageUpload', $img, json);
						return;
					}

					this.placeholder.hide();
					var $figure = $('<' + this.opts.imageTag + '>');

					$img = $('<img>');
					$img.attr('src', json.sizes.full.url);

					//start kent edit
					if (typeof json.alt_text !== 'undefined') {
						$img.attr('alt', json.alt_text);
					}
					if (typeof json.title !== 'undefined') {
						$img.attr('title', json.title);
					}
					//end kent edit

					// set id
					var id = (typeof json.id === 'undefined') ? '' : json.id;
					var type = (typeof json.s3 === 'undefined') ? 'image' : 's3';
					$img.attr('data-' + type, id);

					$figure.append($img);

					//start kent edit
					$img.wrap('<div class="media-wrap"></div>');
					if(json.attribution.author.length > 0 || json.attribution.license.length > 0 ) {
						$attribution = this.imagemanager.buildAttribution(json);
						$img.after($attribution);
					}
					//end kent edit
					//start kent edit
					if(typeof json.caption !== 'undefined') {
						$figcaption = $('<figcaption>' + json.caption + '</figcaption>');
						$figure.append($figcaption);
					}
					//end kent edit



					var pre = this.utils.isTag(this.selection.current(), 'pre');

					if (direct)
					{
						this.air.collapsed();
						this.marker.remove();

						var node = this.insert.nodeToPoint(e, this.marker.get());
						var $next = $(node).next();

						this.selection.restore();

						// buffer
						this.buffer.set();

						// insert
						if (typeof $next !== 'undefined' && $next.length !== 0 && $next[0].tagName === 'IMG')
						{
							// delete callback
							this.core.callback('imageDelete', $next[0].src, $next);

							// replace
							$next.closest('figure, p', this.core.editor()[0]).replaceWith($figure);
							this.caret.after($figure);
						}
						else
						{
							if (pre)
							{
								$(pre).after($figure);
							}
							else
							{
								this.insert.node($figure);
							}

							this.caret.after($figure);
						}

					}
					else
					{
						this.modal.close();

						// buffer
						this.buffer.set();

						// insert
						this.air.collapsed();

						if (pre)
						{
							$(pre).after($figure);
						}
						else
						{
							this.insert.node($figure);
						}

						this.caret.after($figure);
					}

					this.events.dropImage = false;

					this.storage.add({ type: type, node: $img[0], url: $img[0].src, id: id });

					if (direct !== null)
					{
						this.core.callback('imageUpload', $img, json);
					}
				},
		showEdit: function($image)
				  {
					  if (this.events.imageEditing)
					  {
						  return;
				}

					  this.observe.image = $image;

					  var $link = $image.closest('a', this.$editor[0]);

					  this.modal.load('image-edit', this.lang.get('edit'), 705);

					  var $attributionContainer = $('<section id="image-attribution">');
					  this.modal.getActionButton().closest('section').before($attributionContainer);
					  $attributionContainer.append(this.imagemanager.getAttributionFields(true));


					  var $titleSection = this.modal.getModal().find('#redactor-image-title').closest('section');
					  $titleSection.before('<section><label>Name<a id="img-id" target="_blank" href="' + base_url + 'images/edit/" style="float:right; color: #bbb;">ID:<span></span></a></label><strong id="img-name"></strong></section>');
					  $titleSection.after('<section><label>Alt</label><input type="text" id="redactor-image-alt"></section>');
					  $('#redactor-image-align').append('<option value="pull-left">Pull Left</option><option value="pull-right">Pull Right</option>')

					  this.modal.getModal().on('click','.resetValue', function(e){
						  $(this).closest('section').find('input').val($(this).closest('.original').find('.value').text());
					  });

					  this.image.buttonDelete = this.modal.getDeleteButton().text(this.lang.get('delete'));
					  this.image.buttonSave = this.modal.getActionButton().text(this.lang.get('save'));

					  this.image.buttonDelete.on('click', $.proxy(this.image.remove, this));
					  this.image.buttonSave.on('click', $.proxy(this.image.update, this));

					  if (this.opts.imageCaption === false)
					  {
						  $('#redactor-image-caption').val('').hide().prev().hide();
					  }
					  else
					  {
						  var $parent = $image.closest(this.opts.imageTag, this.$editor[0]);
						  var $ficaption = $parent.find('figcaption');
						  if ($ficaption !== 0)
						  {

							  $('#redactor-image-caption').val($ficaption.text()).show();
						  }
					  }

					  if (!this.opts.imagePosition)
					  {
						  $('.redactor-image-position-option').hide();
					  }
					  else
					  {
						  var floatValue = 'none';
						  $classes = $image.closest('figure')[0].classList;
						  if($classes.contains('figure-right')){
							  floatValue = 'right';
						  }else if($classes.contains('figure-left')){
							  floatValue = 'left';
						  }else if($classes.contains('figure-pull-right')){
							  floatValue = 'pull-right';
						  }else if($classes.contains('figure-pull-left')){
							  floatValue = 'pull-left';
						  }else if($classes.contains('figure-center')){
							  floatValue = 'center';
						  }
						  $('#redactor-image-align').val(floatValue);
					  }

					  $('#redactor-image-preview').html($('<img src="' + $image.attr('src') + '" style="max-width: 100%;">'));
					  //start kent edit
					  $('#redactor-image-title').val($image.attr('title'));
					  $('#redactor-image-alt').val($image.attr('alt'));
					  //end kent edit
					  var $redactorImageLink = $('#redactor-image-link');
					  $redactorImageLink.attr('href', $image.attr('src'));
					  if ($link.length !== 0)
					  {
						  $redactorImageLink.val($link.attr('href'));
						  if ($link.attr('target') === '_blank')
						  {
							  $('#redactor-image-link-blank').prop('checked', true);
						  }
					  }

					  // hide link's tooltip
					  $('.redactor-link-tooltip').remove();

					  this.modal.show();

					  // focus
					  if (this.detect.isDesktop())
					  {
						  $('#redactor-image-title').focus();
					  }

					  $imgJson = $.ajax(base_url + 'api/images/' + $image.data('image'),{
						  success:function(json){
							  if(typeof json.error === 'undefined' || json.error === 0) {
								  $('#img-id').attr('href',$('#img-id').attr('href')+json.id).find('span').html(json.id);
								  $('#img-name').html(json.name);

								  var original = $('<div class="original">Default: <span class="value"></span> <button class="resetValue">Reset</button></div>');

								  var title = $('#redactor-image-title');
								  var titleOrginal = original.clone();
								  titleOrginal.find('.value').html(json.title);
								  title.after(titleOrginal);

								  var alt = $('#redactor-image-alt');
								  var altOrginal = original.clone();
								  altOrginal.find('.value').html(json.alt_text);
								  alt.after(altOrginal);

								  var cap = $('#redactor-image-caption');
								  var capOrginal = original.clone();
								  capOrginal.find('.value').html(json.caption);
								  cap.after(capOrginal);

								  $('#redactor-image-attribution-text').val(json.attribution.author);
								  $('#redactor-image-attribution-link').val(json.attribution.link);
								  $('#redactor-image-licence-link').val(json.attribution.license);
							  }
						  }
					  });

				  },
		update: function()
				{
					var $image = this.observe.image;
					var $link = $image.closest('a', this.core.editor()[0]);

					var title = $('#redactor-image-title').val().replace(/(<([^>]+)>)/ig,"");

					//start kent edit
					$image.attr('title', title);
					var alt = $('#redactor-image-alt').val().replace(/(<([^>]+)>)/ig,"");
					$image.attr('alt', alt);
					var attribData = {
						title : title,
						attribution : {
							author:  $('#redactor-image-attribution-text').val(),
							link:    $('#redactor-image-attribution-link').val(),
							license: $('#redactor-image-licence-link').val()
						}
					};

					if(attribData.attribution.author.length > 0 || attribData.attribution.license.length > 0) {
						$attribution = this.imagemanager.buildAttribution(attribData);
						$image.parent().find('.attribution').remove();
						$image.parent().append($attribution);
					}else{
						$image.parent().find('.attribution').remove();
					}

					//end kent edit

					this.image.setFloating($image);

					// as link
					var link = $.trim($('#redactor-image-link').val()).replace(/(<([^>]+)>)/ig,"");
					if (link !== '')
					{
						// test url (add protocol)
						var pattern = '((xn--)?[a-z0-9]+(-[a-z0-9]+)*\\.)+[a-z]{2,}';
						var re = new RegExp('^(http|ftp|https)://' + pattern, 'i');
						var re2 = new RegExp('^' + pattern, 'i');

						if (link.search(re) === -1 && link.search(re2) === 0 && this.opts.linkProtocol)
						{
							link = this.opts.linkProtocol + '://' + link;
						}

						var target = ($('#redactor-image-link-blank').prop('checked')) ? true : false;

						if ($link.length === 0)
						{
							var a = $('<a href="' + link + '" id="redactor-img-tmp">' + this.utils.getOuterHtml($image) + '</a>');
							if (target)
							{
								a.attr('target', '_blank');
							}

							$image = $image.replaceWith(a);
							$link = this.core.editor().find('#redactor-img-tmp');
							$link.removeAttr('id');
						}
						else
						{
							$link.attr('href', link);
							if (target)
							{
								$link.attr('target', '_blank');
							}
							else
							{
								$link.removeAttr('target');
							}
						}
					}
					else if ($link.length !== 0)
					{
						$link.replaceWith(this.utils.getOuterHtml($image));
					}

					this.image.addCaption($image.parent(), []);
					this.modal.close();

					// buffer
					this.buffer.set();

				},
		setFloating: function($image)
				{
					var floating = $('#redactor-image-align').val();

					var $class = '';

					switch (floating)
					{
						case 'left':
							$class = 'figure-left';
							break;
						case 'right':
							$class = 'figure-right';
							break;
						case 'pull-left':
							$class = 'figure-pull-left';
							break;
						case 'pull-right':
							$class = 'figure-pull-right';
							break;
						case 'center':
							$class = 'figure-center';
							break;
					}

					$image.closest('figure').prop('class', $class);
				},

	};
};
*/


/*
TODO config  
var redactor_config = {
	plugins: ['bufferbuttons', 'imagemanager', 'source'],
	imageResizable: false,
	imagePosition: true,
	imageUpload: base_url + 'images/upload',
	imageUploadParam: 'image',
	imageManagerJson: base_url + 'api/images',
	
	pasteLinkTarget: '_blank',
	formatting: ['p', 'h2', 'h3', 'h4'],
	pasteBlockTags: ['h2','h3','h4', 'p', 'blockquote', 'li', 'ol', 'ul'],
	structure: true,
	
	maxHeight: 600,
	videoContainerClass: 'embed-responsive embed-responsive-16by9'
};

*/


var redactor_config = {
	plugins: ['imagemanager'],

	imageResizable: false,
	imagePosition: true,
	imageUpload: base_url + 'images/upload',



	imageUploadExample: function(data, files, e, upload)
	{
		return new Promise(function(resolve, reject)
		{
			var url = base_url + 'images/upload';
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function(e)
			{
				if (xhr.readyState === 4)
				{
					if (xhr.status === 200)
					{
						resolve(xhr.response);
					}
					else
					{
						reject(xhr.status);
					}
				}
			}

			xhr.open('post', url);
			xhr.send();

		}).then(function(response)
		{
			// success
			upload.complete(response);

		}).catch(function(response)
		{
			// fail
			upload.complete(response);
		});
	},




	imageUpload2: function(formData, files, event, upload) {

		console.log(upload);
		console.log(formData);
		// post to url
		var file = files[0];

		var image = {
			image: file
		}
		
		$.ajax({
			type: "POST",
			url: base_url + 'images/upload',
			data: formData,
			success: function (data) {
				console.log(data);
			},
			// dataType: 'json'
		});
		

		/* data should be link 
		$value
			name: "Sibson-Building .....
			type: "image/jpeg"
			tmp_name: "/tmp/phpDw201g"
			error: 0
			size: 669339

		*/
		// return the json from the url

		console.log(formData);
		console.log(files);
		console.log(event);
	},
	imageUploadParam: 'image',
	imageManagerJson: base_url + 'api/images',
	
	styles: true,
	
	buttons: ['format', 'bold', 'italic', 'deleted', 'lists', 'image', 'link', 'undo', 'redo', 'html'],
	formatting: ['p', 'h2', 'h3', 'h4'],
	formattingAdd: {
		'blockquote': {
			title: 'Blockquote',
			api: 'module.block.format',
			args: {
				'tag': 'blockquote',
				'class': 'simple'
			}
		},
		'pullquote': {
			title: 'Pullquote',
			api: 'module.block.format',
			args: {
				'tag': 'blockquote',
				'class': 'simple pull-quote'
			}
		},
		'impact': {
			title: 'Impact Statement',
			api: 'module.block.format',
			args: {
				'tag': 'p',
				'class': 'impact-statement'
			}
		}
	}
};

$('textarea').not('.picker').each( function(){

	if($(this).attr('data-limit')) {
		//Add word limiting logic
		var self = {};
		// Get word limit from item
		self.limit = $(this).attr('data-limit');
		self.limiting_on_words = false;
		self.dom = document.createElement('span');
		self.dom.className = 'text_limits';

		//How much of word limit is left
		self.limit_left = function (field) {
			if (self.limiting_on_words) {
				//word limits
				//See http://stackoverflow.com/questions/6543917/count-number-of-word-from-given-string-using-javascript
				return self.limit - field.split(/\s+\b/).length;
			} else {
				//chars left
				return self.limit - field.length;
			}
		};

		//check the length
		self.checkLength = function (field) {
			// How much if left
			var left = self.limit_left(field);

			// If less than 5, display in Red
			if (left < 5) {
				self.dom.style.color = 'red';
			} else {
				self.dom.style.color = '';
			}

			//show message to user
			if (self.limiting_on_words) {
				self.dom.innerHTML = left + ' words left';
			} else {
				self.dom.innerHTML = left + ' characters left';
			}
		};

		// Work out if limit should be word or char based
		if (self.limit.substring(self.limit.length - 1) == 'w') {
			self.limiting_on_words = true;
			self.limit = parseInt(self.limit.substring(0, self.limit.length - 1));
		} else {
			self.limit = parseInt(self.limit);
		}

		// Add display span
		$(self.dom).insertAfter($(this));



		$(this).redactor($.extend({},redactor_config,{
			callbacks: {
				change: function () {
					var text = this.clean.getPlainText(this.core.editor().text());
					text = text.replace(/\u200B/g, '');
					self.checkLength(text);
				},
				init: function() {
					var text = this.clean.getPlainText(this.core.editor().text());
					text = text.replace(/\u200B/g, '');
					self.checkLength(text);
				}
			}
		}));

	}else{
		$(this).redactor(redactor_config);
	}
});

$('.img-picker textarea').each(function(){
	$(this).redactor($.extend({},redactor_config,{
		picker:true,
		toolbar: false,
		paragraphize: false,
		callbacks: {
			click: function(e) {
				this.image.show();
				//this.button.get('image').callback();
			},
			syncBefore: function(html) {
				return html.replace(/\D/g,'');
			}
		}
	}));
});

$('.img-picker .img-wrapper').click(function(){
	$(this).closest('.img-picker').find('.redactor-editor').click();
});