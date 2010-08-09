/**
 * Loaded by mediatorWidgetFormMediaSelect
 * no init needed
 * @author dalexandre
 * @require Facebox for jQuery
 * @require AjaxForm for jQuery
 * @require jQuery
 */

(function($) {

  $.fn.mediatorMediaLibraryMediaSelect = function(is_multiple) {

    this.each(function() {
      $(this).click(function()
      {
        //console.log('store the ID');
        // Store the clicked widget id on the facebox.
        if (!is_multiple) is_multiple = false;
        $('#facebox').data('widget_field_id', $(this).attr('id')).data('is_multiple', is_multiple);
      });
    });

    return this;
  };

  function searchBinder()
  {
    // When is_multiple, search result must have a "save the selection" button
    if ( $('#facebox').data('is_multiple') )
    {
      dynamicMediaFormApi.init();
    }

    $('#mediator-media-search a.file').click(fileClick);
    $('#mediator-media-search .pagination a').click(function() {
      $('#mediator-media-search').load(this.href, null, function() {
        searchBinder();
      });
      return false;
    });
    $('#mediator_media_library_search_form').ajaxForm(ajaxFormOptionsSearch);
    return false;
  }

  var ajaxFormOptionsSearch = {
    target:     '#mediator-media-search',
    success:    searchBinder
  };

  var ajaxFormOptionsAdd = {
    target:     '#mediator-media-add',
    success:    function() {
      $('#mediator-media-add a.file').click(fileClick);
      $('#mediator-media-description').ajaxForm(ajaxFormOptionsAdd);
      $('#autoselectedmedia a.file').first().click(); // If there is only one file, one choice available, auto-clic on the file.
      if ( $('#facebox').data('is_multiple') &&  $('.mediator_media_library_list').length > 0)
      {
        dynamicMediaFormApi.initAfterUpload();
      }
      return false;
    }
  };

 /**
  * Binding
  */
  $(document).bind('reveal.facebox', function() {
    $(document).trigger('mediatormediarebind')
  });
  $(document).bind('mediatormediarebind', bindForms);


  function bindForms() {
    console.log('Binding');

    $('#facebox form').ajaxForm({
      success: function (responseText) {
        console.log('reveived responseText');
        jQuery.facebox(responseText); // this will launch the internal binding process
        $(document).trigger('mediatormediarebind');
      }
    });

    $('#facebox a').facebox();

    // Make sure files are clickable and that a click on it closes the facebox
    $('#facebox a.file').unbind('click').click(fileClick);
  }

  /**
   * Init
   */
  $(document).ready(function()
  {
    $('a.mediatorWidgetFormMediaSelect[rel*=media_lib_facebox]').facebox();
    $('a.mediatorWidgetFormMediaSelect').mediatorMediaLibraryMediaSelect();
    $('a.mediatorWidgetFormMediaDelete').click(deleteMedia);
  });

  /**
   * Functions
   */
  function fileClick(e)
  {
    // When is_multiple, file click must add to selection the file.
    if ( $('#facebox').data('is_multiple') && $('#autoselectedmedia').length == 0)
    {
      return dynamicMediaFormApi.fileClick(e);
    }

    try
    {
      var image = e.target;
      if ( !$(image).is('img') )
      {
        if ( !$(image).is('a') )
        {
          image = $(image).parent();
        }
        image = $(image).find('img');
      }

      var fieldname = $('#facebox').data('widget_field_id');
      fieldname = fieldname.replace('_link', '');
      $('#' + fieldname).val($(image).parent('a').attr('rel')).change();
      $('#' + fieldname + '_image .imgselected').html($(image).parent().html());
      $('#' + fieldname + '_image a.mediatorWidgetFormMediaSelect').html('Replace');
    }
    catch (exception)
    {
      return false;
    }

    $(document).trigger('close.facebox')
    return false;
  }

})(jQuery);


function deleteMedia(e)
{
  $(e.target).parent().find('.imgselected').html('Media removed').parent().prev('input').val('').removeAttr('value').change().parent().find('a.mediatorWidgetFormMediaSelect').html('Choose a new one');
  return false;
}