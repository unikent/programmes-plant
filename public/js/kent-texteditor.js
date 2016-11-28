$.Redactor.prototype.bufferbuttons = function()
{
	return {
		init: function()
			  {
				  var undo = this.button.addAfter('link','undo', 'Undo');
				  var redo = this.button.addAfter('undo', 'redo', 'Redo');

				  this.button.addCallback(undo, this.buffer.undo);
				  this.button.addCallback(redo, this.buffer.redo);
			  }
	};
};

var redactor_config = {
	plugins: ['bufferbuttons'],
	imageResizable: true,
	imagePosition: true,
	imageFloatMargin: '16px',
	pastePlainText:true,
	pasteLinkTarget: '_blank',
	formatting: ['p', 'h2', 'h3'],
	formattingAdd: {
		"blockquote": {
			title: 'Blockquote',
			args:  ['blockquote', 'class', 'simple']
		},
		"impact": {
			title: 'Impact Statement',
			args:  ['p', 'class', 'impact-statement']
		}
	},
	maxHeight: 600
};

$('textarea').each( function(){

	if($(this).attr('data-limit')) {
		//Add word limiting logic
		var self = {};
		// Get word limit from item
		self.limit = $(this).attr("data-limit");
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