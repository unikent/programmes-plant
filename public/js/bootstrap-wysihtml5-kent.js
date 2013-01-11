$(document).ready(function (){

//Allow bootstrap-wysihtml template/lang to work similar to how they did before.
var bootstrap_toolbar = function() {

    var language = {
        en: {
                font_styles: {
                    normal: "Normal text",
                    h1: "Heading 1",
                    h2: "Heading 2",
                    h3: "Heading 3",
                    h4: "Heading 4",
                    h5: "Heading 5",
                    h6: "Heading 6"
                },
                emphasis: {
                    bold: "Bold",
                    italic: "Italic",
                    underline: "Underline"
                },
                lists: {
                    unordered: "Unordered list",
                    ordered: "Ordered list",
                    outdent: "Outdent",
                    indent: "Indent"
                },
                link: {
                    insert: "Insert link",
                    cancel: "Cancel"
                },
                image: {
                    insert: "Insert image",
                    cancel: "Cancel"
                },
                html: {
                    edit: "Edit HTML"
                },
                colours: {
                    black: "Black",
                    silver: "Silver",
                    gray: "Grey",
                    maroon: "Maroon",
                    red: "Red",
                    purple: "Purple",
                    green: "Green",
                    olive: "Olive",
                    navy: "Navy",
                    blue: "Blue",
                    orange: "Orange"
                }
        }
    }

    var templates = {
        "font-styles" : function(locale) {
            return "<li class='dropdown'>" +
                  "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'>" +
                  "<i class='icon-font'></i>&nbsp;<span class='current-font'>" + locale.font_styles.normal + "</span>&nbsp;<b class='caret'></b>" +
                  "</a>" +
                  "<ul class='dropdown-menu'>" +
                    "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='div'>" + locale.font_styles.normal + "</a></li>" +
                    "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h2'>" + locale.font_styles.h2 + "</a></li>" +
                    "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h3'>" + locale.font_styles.h3 + "</a></li>" +
                    "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h4'>" + locale.font_styles.h4 + "</a></li>" +
                    "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h5'>" + locale.font_styles.h5 + "</a></li>" +
                    "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h6'>" + locale.font_styles.h6 + "</a></li>" +
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

    this.cache = false;
    //Generate a toolbar
    this.gen = function(){
        var html = '';
        //Use cached template if possible
        if(!this.cache){
            for(var t in templates){
                html += templates[t](language.en)
            }
        }else{
            html = this.cache;
        }
        //return in ul container
        var container = document.createElement('ul');
        container.className = 'wysihtml5-toolbar';
        container.innerHTML = html;
        return container;
    }
}

    //Create instance of toolbar generator
    var toolbar_generator =  new bootstrap_toolbar();
    //For each text area
    $('textarea').each( function(){
        var toolbar_node = $(toolbar_generator.gen()).insertBefore($(this));
        new wysihtml5.Editor($(this)[0], { // id of textarea element
            toolbar: toolbar_node[0], // id of toolbar element
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
        });
    });
});