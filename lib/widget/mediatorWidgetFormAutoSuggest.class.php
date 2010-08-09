<?php

class mediatorWidgetFormAutoSuggest extends sfWidgetFormInput
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('url');
    $this->addOption('config', '{ minChars: 2, matchCase: true, retrieveLimit: 10, startText: "" }');

    parent::configure($options, $attributes);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $prefix = $this->generateId($name);

    return parent::render($name, null, $attributes, $errors).
      sprintf(<<<EOF
<script type="text/javascript">
  jQuery(document).ready(function() {

    $('a[rel=tag_helper_suggest]').facebox();
    $('#facebox a[rel=tag_helper_suggest]').remove();
    jQuery("#%s")
    .autoSuggest(
      "%s",
      %s,
      "%s"
    );
  });
</script>
EOF
      ,
      $prefix,
      $this->getOption('url'),
      $this->getOption('config'),
      strtolower($value)
    );
  }
}
