<?php use_helper('I18N') ?>
<div id="sf_admin_container" class="mediator-media-library">
  <h1><?php echo __('Media Library') ?></h1>
  <?php  include_component('mediatorMediaLibrary', 'folder_breadcrumb', array('mm_media_folder' => $mm_media_folder)); ?>

  <div id="mediator_media_library_sidebar">
    <?php include_component('mediatorMediaLibrary', 'search', array('mm_media_folder' => $mm_media_folder)); ?>
    <?php include_partial('mediatorMediaLibrary/list_actions', array('mm_media_folder' => $mm_media_folder)); ?>
  </div>
  <?php include_partial('mediatorMediaLibrary/flash'); ?>
  <?php include_component('mediatorMediaLibrary', 'list', array('mm_media_folder' => $mm_media_folder)); ?>
  <div class="spacer"></div>
</div>