<?php

/**
 * sfWidgetFormInput represents an HTML input tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormInput.class.php 9046 2008-05-19 08:13:51Z FabianLange $
 */
class sfWidgetFormCleverMediaLibraryImageSelect extends sfWidgetFormInput
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * type: The widget type (text by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addOption('path', '');
    $this->setOption('type', 'hidden');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $response = sfContext::getInstance()->getResponse();
//    $response->addJavascript('/cleverMediaLibraryPlugin/js/jquery.js');
    $response->addJavascript('/cleverMediaLibraryPlugin/js/facebox.js');
    $response->addJavascript('/cleverMediaLibraryPlugin/js/jquery.livequery.js');
    $response->addJavascript('/cleverMediaLibraryPlugin/js/cleverMediaLibrary.js');
    $response->addStylesheet('/cleverMediaLibraryPlugin/css/facebox.css');

    $id = $this->generateId($name);
    $image = $this->display_value($value, $id);
    $image_div = content_tag('div', $image, array('id' => $id.'_image'));
    return parent::render($name, $value, $attributes, $errors).$image_div;
  }

  protected function display_value($value, $id)
  {
    $choose_link = link_to(
      'choose',
      sprintf('@cleverMediaLibrary?action=chooseImage&fieldname=%s&path=%s', $id, $this->getOption('path')),
      array('rel' => 'facebox')
    );

    $choose_link .= sprintf(<<<EOF
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
  jQuery(document).bind('reveal.facebox', function() {
    jQuery('#facebox a[class!=close]').each(function() {
      var fieldname = $('#facebox #cc_media_librart_widget_fieldname').val();
      var href = $(this).attr('href');
      if (-1 === (href + '').indexOf('?fieldname=', 0)) {
        $(this).attr('href', $(this).attr('href') + '?fieldname=' + fieldname);
      }
    });
  });

  jQuery('#facebox a.file').livequery('click', function() {
    var fieldname = $('#facebox #cc_media_librart_widget_fieldname').val();
    jQuery('#' + fieldname).val($(this).attr('id'));
    jQuery('#' + fieldname + '_image').html('<img src="' + $('img', this).attr('src') + '" alt="" /><br />' + '%s');
    $(document).unbind('keydown.facebox')
    $('#facebox').fadeOut(function() {
      $('#facebox .content').removeClass().addClass('content');
      $('#facebox_overlay').fadeOut(200, function(){
        $("#facebox_overlay").removeClass("facebox_overlayBG")
        $("#facebox_overlay").addClass("facebox_hide")
        $("#facebox_overlay").remove()
      });
      $('#facebox .loading').remove()
    })
    return false;
  });
});
//]]>
</script>
EOF
,
$choose_link);

    if (!is_null($value))
    {
      $cc_media = Doctrine::getTable('CcMedia')->find($value);

      if ($cc_media)
      {
        return image_tag(sfConfig::get('app_cleverMediaLibraryPlugin_media_root', 'media')
                         .'/'.cleverMediaLibraryToolkit::getDirectoryForSize('small')
                         .'/'.$cc_media->getCcMediaFolder()->getAbsolutePath()
                         .'/'.$cc_media->getThumbnailFilename()).'<br />'.$choose_link;
      }
    }

    return sfContext::getInstance()->getI18N()->__('No image has been chosen so far.').'<br />'
    .$choose_link;
  }
}
