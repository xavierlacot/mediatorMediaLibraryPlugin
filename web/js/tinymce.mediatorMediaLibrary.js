(function($){

var editor;

/**
 * Initialization
 */
$.fn.mediatorMediaLibraryMediaSelect = function(is_multiple) {
  this.each(function() {
    $(this).click(function() {
      // Store the clicked widget id on the facebox.
      if (!is_multiple) is_multiple = false;
      $('body').attr('widget_field_id', $(this).attr('id')).attr('is_multiple', is_multiple);
    });
  });

  return this;
};

/**
* Binding
*/
$(document).bind('reveal.facebox', function() {
  $('html').trigger('mediatormediarebind')
});

$(document).delegate('html', 'mediatormediarebind', mediatorMediaRebind);


function mediatorMediaRebind() {
  //console.log('beginning mediatorMediaRebind()');

  // ajaxify forms
  $('#facebox form[id!=mediator-media-add-form]').ajaxForm({
    success: function (responseText) {
      jQuery.facebox(responseText);
      $('html').trigger('mediatormediarebind');
    }
  });

  // enable facebox on all links wthin the popup
  if ($('#facebox .ui-tabs-panel').length > 0) {
    // only in the panels if there are
    //console.log('binding panels only');
    $('#facebox .content .ui-tabs-panel a').unbind('click').facebox();
  } else {
    // else globaly
    //console.log('binding globaly');
    $('#facebox .content a').unbind('click').facebox();
  }

  // Make sure files are clickable and that a click on it closes the facebox
  $('#facebox a.file').unbind('click').click(fileClick);
}

/**
 * Init
 */
$(document).ready(function() {
  if ($('#facebox', document).length == 0 && jQuery.facebox) {
    $('a.mediatorWidgetFormMediaSelect[rel*=media_lib_facebox]').unbind('click').facebox();
    $('a.mediatorWidgetFormMediaSelect').mediatorMediaLibraryMediaSelect();
  }
});


/**
 * Functions
 */
function fileClick(e) {
  try {
    var target = e.target;
    editor.execCommand('mceInsertContent', false, '<img src="http://picmybox.fr' + $(target).attr('src').replace('/small/', '/original/') + '" />', {skip_undo : 1});
    editor.undoManager.add();
  } catch (exception) {
    return false;
  }

  $(document).trigger('close.facebox')
  return false;
}


/**
 * Media Manager Submit button
 */
$(function(){
  if (typeof tinymce != 'undefined') {
  	tinymce.create('tinymce.plugins.mediatorMediaLibraryPlugin', {
  		init : function(ed, url) {
        editor = ed;

  			// Register commands
  			ed.addCommand('mceMediatorMediaLibrary', function() {
          jQuery.facebox({ ajax: '/mediatorMediaLibrary/choose' });
  			});

  			// Register button
  			ed.addButton('mediator', {
  				title : 'mediator Media Library',
  				cmd : 'mceMediatorMediaLibrary',
  				image : '/mediatorMediaLibraryPlugin/images/tinymce_button.png',
  			});
  		},

  		getInfo : function() {
  			return {
  				longname : 'Mediator Media Library',
  				author : 'Xavier Lacot',
  				authorurl : 'http://www.lacot.org/',
  				infourl : '',
  				version : '0.1.0'
  			};
  		}
  	});

  	// Register tinyMCE hooks
	  tinymce.PluginManager.add('mediatorMediaLibrary', tinymce.plugins.mediatorMediaLibraryPlugin);
  }
});

})(jQuery);
