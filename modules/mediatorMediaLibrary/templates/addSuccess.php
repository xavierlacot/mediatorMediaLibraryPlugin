<?php use_helper('I18N') ?>
<div id="sf_admin_container" class="mediator-media-library">
  <h1><?php echo __('Add a new media') ?></h1>
  <?php include_component('mediatorMediaLibrary', 'folder_breadcrumb', array('mm_media_folder' => $mm_media_folder)); ?>

  <div class="sf_admin_form">
    <?php echo form_tag_for($form, 'mediatorMediaLibrary/add') ?>
      <?php include_partial('mediatorMediaLibrary/flash'); ?>
      <?php include_partial('mediatorMediaLibrary/form', array('form' => $form, 'mm_media_folder' => $mm_media_folder)); ?>
    </form>
  </div>
</div>