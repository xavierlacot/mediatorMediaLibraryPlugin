<?php use_stylesheet(sfConfig::get('sf_admin_module_web_dir').'/css/global.css', 'first') ?>
<?php use_stylesheet(sfConfig::get('sf_admin_module_web_dir').'/css/default.css', 'first') ?>

<?php if ($sf_request->isXmlHttpRequest()): // Add this CSS only when there is no layout
  echo stylesheet_tag('/mediatorMediaLibraryPlugin/css/mediatorMediaLibrary.css');
endif; ?>