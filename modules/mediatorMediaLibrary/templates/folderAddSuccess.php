<?php use_helper('I18N') ?>
<div id="sf_admin_container" class="mediator-media-library">
  <h1><?php echo __('Create a subfolder') ?></h1>
  <?php include_component('mediatorMediaLibrary', 'folder_breadcrumb', array('mm_media_folder' => $mm_media_folder)); ?>

  <div class="sf_admin_form">
    <?php echo form_tag_for($form, 'mediatorMediaLibrary/folderAdd') ?>
      <?php include_partial('mediatorMediaLibrary/flash'); ?>
      <?php include_partial('mediatorMediaLibrary/folder_form', array('form' => $form, 'mm_media_folder' => $mm_media_folder)); ?>
    </form>
  </div>
</div>