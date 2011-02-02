/**
 * Loaded by mediatorWidgetFormMediaSelect
 * no init needed
 * @author Damien Alexandre
 * @author Xavier Lacot
 * @require Facebox for jQuery
 * @require AjaxForm for jQuery
 * @require jQuery
 */

(function($) {

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
    if ($('#facebox', document).length == 0) {
      $('a.mediatorWidgetFormMediaSelect[rel*=media_lib_facebox]').unbind('click').facebox();
      $('a.mediatorWidgetFormMediaSelect').mediatorMediaLibraryMediaSelect();
      $('a.mediatorWidgetFormMediaDelete').unbind('click').click(deleteMedia);
    }
  });


  /**
   * Functions
   */
  function fileClick(e) {
    try {
      var target = e.target;

      if (!$(target).is('a')) {
        target = $(target).parents('.file');
      }

      var fieldname = $('body').attr('widget_field_id');
      fieldname = fieldname.replace('_link', '');
      $('#' + fieldname).val($(target).attr('rel')).change();
      $('#' + fieldname + '_image .imgselected').html($(target).html());
      $('#' + fieldname + '_image a.mediatorWidgetFormMediaSelect').html('Replace');
    } catch (exception) {
      return false;
    }

    $(document).trigger('close.facebox')
    return false;
  }
})(jQuery);


function deleteMedia(e) {
  $(e.target).parent().find('.imgselected').html('Media removed').parent().prev('input').val('').removeAttr('value').change().parent().find('a.mediatorWidgetFormMediaSelect').html('Choose a new one');
  return false;
}