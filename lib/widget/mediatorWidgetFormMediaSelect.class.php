<?php

/**
 * mediatorWidgetFormMediaSelect represents an HTML hidden input tag, with
 * javascript selection, media display...
 */
class mediatorWidgetFormMediaSelect extends sfWidgetFormInput
{
  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addOption('path', '');
    $this->addOption('allowImage', true);
    $this->addOption('allowVideo', true);
    $this->addOption('allowAudio', true);
    $this->addOption('allowOther', true);
    $this->setOption('type', 'hidden');
  }

  protected function get_search_options()
  {
    $search_options = "image=". ($this->getOption('allowImage') ? '1' : '0');
    $search_options .= "&video=". ($this->getOption('allowVideo') ? '1' : '0');
    $search_options .= "&audio=". ($this->getOption('allowAudio') ? '1' : '0');
    $search_options .= "&other=". ($this->getOption('allowOther') ? '1' : '0');
    return $search_options;
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
    $id = $this->generateId($name);
    $image = $this->display_value($value, $id);
    $image_div = content_tag('div', $image, array('id' => $id.'_image', 'class' => 'mediatorMediaLibrary_widget'));

    return parent::render($name, $value, $attributes, $errors).$image_div;
  }

  protected function display_value($value, $id)
  {
    sfProjectConfiguration::getActive()->loadHelpers(array('Text', 'Date'));

    // Media selection link
    $choose_link = link_to(
      sfContext::getInstance()->getI18N()->__('Choose'),
      sprintf('mediatorMediaLibrary/choose?path=%s&fieldname=%s&getoldpath=true', $this->getOption('path'), $id),
      array(
        'rel' => 'media_lib_facebox',
        'id' => $id.'_link',
        'class' => 'mediatorWidgetFormMediaSelect',
        'query_string' => $this->get_search_options()
      )
    );
    // Media edit link
    $edit_link = link_to(
      sfContext::getInstance()->getI18N()->__('Replace'),
      sprintf('mediatorMediaLibrary/choose?path=%s&fieldname=%s&getoldpath=true', $this->getOption('path'), $id),
      array(
        'rel' => 'media_lib_facebox',
        'id' => $id.'_link',
        'class' => 'mediatorWidgetFormMediaSelect',
        'query_string' => $this->get_search_options()
      )
    );
    // Media deletion link
    $delete_link = link_to(
      sfContext::getInstance()->getI18N()->__('Delete'),
      'mediatorMediaLibrary/index',
      array(
        'rel' => 'delete',
        'class' => 'mediatorWidgetFormMediaDelete'
      )
    );

    if (!is_null($value))
    {
      $media = Doctrine::getTable('MmMedia')->find($value);

      if ($media)
      {
        return '<div class="imgselected">'.$media->getDisplay(array('size' => 'small')).'
                <strong>'.truncate_text($media->getTitle(), 80).'</strong>
                <span>'.truncate_text($media->getBody(), 85).'</span>
                <em>Ajouté le '.format_date($media->getCreatedAt()).'</em>
            </div> '.$edit_link.' '.$delete_link;
      }
    }

    return '<div class="imgselected">'.sfContext::getInstance()->getI18N()->__('No file has been chosen so far.').'</div>'
    .$choose_link.' '.$delete_link;
  }

  public function getJavascripts()
  {
    return array(
      '/mediatorMediaLibraryPlugin/js/facebox.js',
      '/mediatorMediaLibraryPlugin/js/jquery.autoSuggest.js',
      '/mediatorMediaLibraryPlugin/js/jquery.form.js',
      '/mediatorMediaLibraryPlugin/js/jquery.uploadify/swfobject.js',
      '/mediatorMediaLibraryPlugin/js/jquery.uploadify/jquery.uploadify.v2.1.0.min.js',
      '/mediatorMediaLibraryPlugin/js/jquery.select.mediatorMediaLibrary.js',
    );
  }

  public function getStylesheets()
  {
    return array(
      '/mediatorMediaLibraryPlugin/js/jquery.uploadify/uploadify.css' => 'all',
      '/mediatorMediaLibraryPlugin/css/facebox.css' => 'all',
      '/mediatorMediaLibraryPlugin/css/autoSuggest.css' => 'all',
      '/mediatorMediaLibraryPlugin/css/mediatorMediaLibrary.css' => 'all',
    );
  }
}
