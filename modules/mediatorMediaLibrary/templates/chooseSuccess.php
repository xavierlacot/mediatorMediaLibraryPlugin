<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>
    <?php use_helper('mediatorMediaLibrary'); ?>
    <input type="hidden" name="mm_media_library_widget_fieldname" id="mm_media_library_widget_fieldname" value="<?php echo $sf_request->getParameter('fieldname') ?>" />

    <?php use_helper('I18N') ?>
    <?php include_partial('mediatorMediaLibrary/assets') ?>
    <div id="sf_admin_container" class="mediator-media-library">
      <?php include_partial('mediatorMediaLibrary/flash'); ?>

      <div id="tabs">
        <ul>
          <li><a href="#mediator-media-browse"><?php echo __('Browse') ?></a></li>
          <li><a href="#mediator-media-search"><?php echo __('Search') ?></a></li>
        </ul>

        <div id="mediator-media-browse" class="mediator-media-library">
          <h1><?php echo __('Browse media Library') ?></h1>
          <?php  include_component('mediatorMediaLibrary', 'folder_breadcrumb', array('mm_media_folder' => $mm_media_folder, 'action' => 'choose')); ?>

          <div id="mediator_media_library_sidebar">
            <?php include_partial('mediatorMediaLibrary/choose_actions', array('mm_media_folder' => $mm_media_folder)); ?>
          </div>
          <?php include_partial('mediatorMediaLibrary/browse', array('mm_media_folder' => $mm_media_folder, 'allowed_types' => $allowed_types, 'action' => 'choose')) ?>
        </div>

        <div id="mediator-media-search" class="mediator-media-library">
          <h1><?php echo __('Search in media Library') ?></h1>
          <?php include_partial('mediatorMediaLibrary/advanced_search', array('mm_media_folder' => $mm_media_folder, 'allowed_types' => $allowed_types)); ?>
          <div class="spacer"></div>
        </div>
      </div>
    </div>

    <script type="text/javascript">
      $(document).ready(function() {
    		$("#tabs").tabs();
      });
    </script>
  </body>
</html>