<?php
/**
 * Description of sfWidgetFormMediatorTextareaTinyMCE
 *
 * @author xlacot
 */
class sfWidgetFormMediatorTextareaTinyMCE extends sfWidgetFormTextarea
{

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * theme:  The Tiny MCE theme
   *  * width:  Width
   *  * height: Height
   *  * config: The javascript configuration
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('theme', 'advanced');
    $this->addOption('width');
    $this->addOption('height');
    $this->addOption('config', '');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
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
//    $response->addJavascript('/mediatorMediaLibraryPlugin/js/facebox.js');
//    $response->addJavascript('/mediatorMediaLibraryPlugin/js/jquery.livequery.js');
//    $response->addJavascript('/mediatorMediaLibraryPlugin/js/mediatorMediaLibrary.js');
    $response->addStylesheet('/mediatorMediaLibraryPlugin/css/facebox.css');
    $response->addStylesheet('/mediatorMediaLibraryPlugin/css/mediatorMediaLibrary.css');
    $textarea = parent::render($name, $value, $attributes, $errors);

    $js = sprintf(<<<EOF
        <script type="text/javascript">
  tinyMCE.init({
    mode:                              "exact",
    elements:                          "%s",
    theme:                             "%s",
    %s
    %s
    theme_advanced_toolbar_location:   "top",
    theme_advanced_toolbar_align:      "left",
    theme_advanced_statusbar_location: "bottom",
    theme_advanced_resizing:           true,
    plugins:                            "mediatorMediaLibrary",
    mediatorMediaLibrary:                 {uri : "%s", sendTo: ""},
  	theme_advanced_buttons1 : "bold,mediator,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,link,unlink,mediator",
  	theme_advanced_buttons2 : "bullist,numlist,separator,outdent,indent,separator,undo,redo,separator",
    theme_advanced_buttons3 : "hr,removeformat,visualaid,separator,sub,sup,separator,charmap"
    %s
  });
</script>
EOF
        ,
        $this->generateId($name),
        $this->getOption('theme'),
        $this->getOption('width')  ? sprintf('width:                             "%spx",', $this->getOption('width')) : '',
        $this->getOption('height') ? sprintf('height:                            "%spx",', $this->getOption('height')) : '',
        sfContext::getInstance()->getController()->genUrl('mediatorMediaLibrary/tinyMceBrowse'),
        $this->getOption('config') ? ",\n".$this->getOption('config') : ''
    );

    return $textarea.$js;
  }

}
?>
