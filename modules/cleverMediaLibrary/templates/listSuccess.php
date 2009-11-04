<?php use_helper('I18N') ?>
<div id="sf_admin_container" class="clever-media-library">
  <h1><?php echo __('Media Library') ?></h1>
  <?php  include_component('cleverMediaLibrary', 'folder_breadcrumb', array('cc_media_folder' => $cc_media_folder)); ?>

  <div id="clever_media_library_sidebar">
    <?php include_component('cleverMediaLibrary', 'search', array('cc_media_folder' => $cc_media_folder)); ?>
    <?php include_partial('cleverMediaLibrary/list_actions', array('cc_media_folder' => $cc_media_folder)); ?>
  </div>
  <?php include_partial('cleverMediaLibrary/flash'); ?>
  <?php include_component('cleverMediaLibrary', 'list', array('cc_media_folder' => $cc_media_folder)); ?>
</div>