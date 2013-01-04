
        var new_locale = $.fn.wysihtml5.locale = {
            font_styles: {
                h4: "Heading 4",
                h5: "Heading 5",
                h6: "Heading 6"
            }
        };
        
        var myCustomTemplates = {
            "font-styles" : function(locale) {
                return "<li class='dropdown'>" +
                      "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'>" +
                      "<i class='icon-font'></i>&nbsp;<span class='current-font'>" + locale.font_styles.normal + "</span>&nbsp;<b class='caret'></b>" +
                      "</a>" +
                      "<ul class='dropdown-menu'>" +
                        "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='div'>" + locale.font_styles.normal + "</a></li>" +
                        "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h2'>" + locale.font_styles.h2 + "</a></li>" +
                        "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h3'>" + locale.font_styles.h3 + "</a></li>" +
                        "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h4'>" + new_locale.font_styles.h4 + "</a></li>" +
                        "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h5'>" + new_locale.font_styles.h5 + "</a></li>" +
                        "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h6'>" + new_locale.font_styles.h6 + "</a></li>" +
                      "</ul>" +
                      "</li>";
            },
            "emphasis": function(locale) {
            return "<li>" +
              "<div class='btn-group'>" +
                "<a class='btn' data-wysihtml5-command='bold' title='CTRL+B'>" + locale.emphasis.bold + "</a>" +
                "<a class='btn' data-wysihtml5-command='italic' title='CTRL+I'>" + locale.emphasis.italic + "</a>" +
              "</div>" +
            "</li>";
            },
            "lists": function(locale) {
            return "<li>" +
              "<div class='btn-group'>" +
                "<a class='btn' data-wysihtml5-command='insertUnorderedList' title='" + locale.lists.unordered + "'><i class='icon-list'></i></a>" +
                "<a class='btn' data-wysihtml5-command='insertOrderedList' title='" + locale.lists.ordered + "'><i class='icon-th-list'></i></a>" +
              "</div>" +
            "</li>";
            },
            "link": function(locale) {
                return "<li>" +
                  "<div class='bootstrap-wysihtml5-insert-link-modal modal hide fade'>" +
                    "<div class='modal-header'>" +
                      "<a class='close' data-dismiss='modal'>&times;</a>" +
                      "<h3>" + locale.link.insert + "</h3>" +
                    "</div>" +
                    "<div class='modal-body'>" +
                      "<input value='http://' class='bootstrap-wysihtml5-insert-link-url input-xlarge'>" +
                    "</div>" +
                    "<div class='modal-footer'>" +
                      "<a href='#' class='btn' data-dismiss='modal'>" + locale.link.cancel + "</a>" +
                      "<a href='#' class='btn btn-primary' data-dismiss='modal'>" + locale.link.insert + "</a>" +
                    "</div>" +
                  "</div>" +
                  "<a class='btn' data-wysihtml5-command='createLink' title='" + locale.link.insert + "'><i class='icon-link'></i></a>" +
                "</li>";
            },
            "html": function(locale) {
                return "<li>" +
                "<div class='btn-group'>" +
                "<a class='btn' data-wysihtml5-action='change_view' title='" + locale.html.edit + "'>HTML</a>" +
                "</div>" +
                "</li>";
           }
            
        };


        // wysiwyg
        $('textarea').each( function(){
            
            var textarea = $(this);
            $(this).wysihtml5('deepExtend', {
            
            "customTemplates": myCustomTemplates,
            "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
        	"emphasis": true, //Italics, bold, etc. Default true
        	"lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
        	"link": true, //Button to insert a link. Default true
        	"image": false, //Button to insert an image. Default true
        	"html" : true,
        	"events": {
        		"paste": function() { 
        			//alert("Watch it!");
        		},
        		// highlight the html button and textarea when in html mode
        		"change_view": function() {
        		  var changeViewSelector = "a[data-wysihtml5-action='change_view']";
        		  textarea.parent().find(changeViewSelector).toggleClass('btn-warning');
        		  textarea.css('border-color', 'orange');
        		}
        	},
        	"useLineBreaks": false,
        	
        	"parserRules": {
                "tags": {
                    "p": {},
                    "strong": {},
                    "em": {},
                    "h1": {
                        "rename_tag": "h2"
                    },
                    "h4": {},
                    "h5": {},
                    "h6": {},
                    "b": {
                        "rename_tag": "strong"
                    },
                    "i": {
                        "rename_tag": "em"
                    }
                }
            }
        
        
        	
        	}) // wysihtml5
        }); // textarea
        