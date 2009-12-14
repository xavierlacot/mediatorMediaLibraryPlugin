/**
 * mediatorMediaLibrary object used in TinyMCE popup mode
 */
if (typeof tinyMCEPopup != 'undefined')
{
  var mediatorMediaLibrary = {
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
        tinyMCEPopup.getWin().jQuery('#' + sendTo).val(params.id).trigger('change');
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
  tinyMCEPopup.onInit.add(mediatorMediaLibrary.init, mediatorMediaLibrary);
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
  	tinymce.create('tinymce.plugins.mediatorMediaLibraryPlugin', {
  		init : function(ed, url) {
  			// Register commands
  			ed.addCommand('mceMediatorMediaLibrary', function() {
  				var e = ed.selection.getNode();

  				// Internal image object like a flash placeholder
  				if (ed.dom.getAttrib(e, 'class').indexOf('mceItem') != -1)
  					return;

  				ed.windowManager.open({
  					file : ed.getParam('mediatorMediaLibrary').uri,
            name: 'mediator',
  					width : 600,
  					height : 400,
            resizable : 'yes',
            scrollbars: 'yes'
  				}, {
  					plugin_url :   url,
            sendTo:        ed.getParam('mediatorMediaLibrary').sendTo
  				});
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