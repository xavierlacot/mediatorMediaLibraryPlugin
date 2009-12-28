<?php

/**
 * sfWidgetFormInput represents an HTML input tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormInput.class.php 9046 2008-05-19 08:13:51Z FabianLange $
 */
class sfWidgetFormMediatorMediaLibraryImageSelect extends sfWidgetFormInput
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
//    $response->addJavascript('/mediatorMediaLibraryPlugin/js/jquery.js');
//    $response->addJavascript('/mediatorMediaLibraryPlugin/js/facebox.js');
//    $response->addJavascript('/mediatorMediaLibraryPlugin/js/jquery.livequery.js');
//    $response->addJavascript('/mediatorMediaLibraryPlugin/js/mediatorMediaLibrary.js');
//    $response->addStylesheet('/mediatorMediaLibraryPlugin/css/facebox.css');

    $id = $this->generateId($name);
    $image = $this->display_value($value, $id);
    $image_div = content_tag('div', $image, array('id' => $id.'_image'));
    return parent::render($name, $value, $attributes, $errors).$image_div;
  }

  protected function display_value($value, $id)
  {
    $choose_link = link_to(
      'choose',
      sprintf('@mediatorMediaLibrary?action=chooseImage&fieldname=%s&path=%s', $id, $this->getOption('path')),
      array('rel' => 'facebox')
    );

    $choose_link .= sprintf(<<<EOF
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
  jQuery(document).bind('reveal.facebox', function() {
    jQuery('#facebox a[class!=close]').each(function() {
      var fieldname = jQuery('#facebox #mm_media_librart_widget_fieldname').val();
      var href = jQuery(this).attr('href');
      if (-1 === (href + '').indexOf('?fieldname=', 0)) {
        jQuery(this).attr('href', jQuery(this).attr('href') + '?fieldname=' + fieldname);
      }
    });
  });

  jQuery('#facebox a.file').livequery('click', function() {
    var fieldname = jQuery('#facebox #mm_media_librart_widget_fieldname').val();
    jQuery('#' + fieldname).val(jQuery(this).attr('id'));
    jQuery('#' + fieldname + '_image span.imgselected').html('<img src="' + jQuery('img', this).attr('src') + '" alt="" />');
    jQuery(document).unbind('keydown.facebox')
    jQuery('#facebox').fadeOut(function() {
      jQuery('#facebox .content').removeClass().addClass('content');
      jQuery('#facebox_overlay').fadeOut(200, function(){
        jQuery("#facebox_overlay").removeClass("facebox_overlayBG")
        jQuery("#facebox_overlay").addClass("facebox_hide")
        jQuery("#facebox_overlay").remove()
      });
      jQuery('#facebox .loading').remove()
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
      $mm_media = Doctrine::getTable('mmMedia')->find($value);

      if ($mm_media)
      {
        return '<span class="imgselected">'.image_tag(sfConfig::get('app_mediatorMediaLibraryPlugin_media_root', 'media')
                         .'/'.mediatorMediaLibraryToolkit::getDirectoryForSize('small')
                         .'/'.$mm_media->getmmMediaFolder()->getAbsolutePath()
                         .'/'.$mm_media->getThumbnailFilename()).'</span><br />'.$choose_link;
      }
    }

    return sfContext::getInstance()->getI18N()->__('<span class="imgselected">No image has been chosen so far.</span>').'<br />'
    .$choose_link;
  }
}
