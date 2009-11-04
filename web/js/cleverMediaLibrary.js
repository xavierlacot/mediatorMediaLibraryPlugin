
/**
 * cleverMediaLibrary object used in TinyMCE popup mode
 */
if (typeof tinyMCEPopup != 'undefined')
{
  var cleverMediaLibrary = {
    /**
     * Initializing the popup window
     */
    init: function(){

    },
    /**
     * Send back an image to the text editor
     */
    doInsertImage: function(uri, params){
      var ed = tinyMCEPopup.editor;
      var sendTo = tinyMCEPopup.getWindowArg('sendTo');

      if (sendTo)
      {
        tinyMCEPopup.getWin().jQuery('#'+sendTo).val(params.id).trigger('change');
        setTimeout('tinyMCEPopup.close()', 500);
      }
      else{
        ed.execCommand('mceInsertContent', false, '<img id="__mce_tmp" src="javascript:;" />', {skip_undo : 1});
    		ed.dom.setAttribs('__mce_tmp', {
          src: uri,
          alt: params.alt
        });
    		ed.dom.setAttrib('__mce_tmp', 'id', '');
    		ed.undoManager.add();
      }
    }
  };
  tinyMCEPopup.onInit.add(cleverMediaLibrary.init, cleverMediaLibrary);
}


/**
 * TinyMCE install of custom plugin
 * @param {Object} $
 */
(function($){
/**
 * Media Manager Submit button
 */
$(function(){
  if (typeof tinymce != 'undefined') {
  	tinymce.create('tinymce.plugins.cleverMediaLibrary', {
  		init : function(ed, url) {
  			// Register commands
  			ed.addCommand('cleverMediaLibrary', function() {
  				var e = ed.selection.getNode();

  				// Internal image object like a flash placeholder
  				if (ed.dom.getAttrib(e, 'class').indexOf('mceItem') != -1)
  					return;

  				ed.windowManager.open({
  					file : ed.getParam('vodkamm').uri,
            name: 'cleverMediaLibrary',
  					width : screen.width,
  					height : screen.height,
            resizable : 'yes',
            scrollbars: 'yes'
  				}, {
  					plugin_url : url,
            sendTo:        ed.getParam('vodkamm').sendTo
  				});
  			});

  			// Register buttons
  			ed.addButton('image', {
  				title : 'Clever Media Library',
  				cmd : 'cleverMediaLibrary'
  			});
  		},

  		getInfo : function() {
  			return {
  				longname : 'Clever Media Library',
  				author : 'Clever Age',
  				authorurl : 'http://www.clever-age.com/',
  				infourl : '',
  				version : '1.0.0'
  			};
  		}
  	});

  	// Register tinyMCE hooks
	  tinymce.PluginManager.add('cleverMediaLibrary', tinymce.plugins.cleverMediaLibrary);
  }
});

/**
 * Facebox media choice window
 */
$(function(){
  $('a[rel*=facebox]').livequery(function() {
    $(this).facebox();
  });
});

})(jQuery);


