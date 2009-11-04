<?php use_helper('I18N') ?>
<div id="sf_admin_container" class="clever-media-library">
  <h1><?php echo __('Add a new media') ?></h1>
  <?php include_component('cleverMediaLibrary', 'folder_breadcrumb', array('cc_media_folder' => $cc_media_folder)); ?>

  <div class="sf_admin_form">
    <?php echo form_tag_for($form, 'cleverMediaLibrary/add') ?>
      <?php include_partial('cleverMediaLibrary/flash'); ?>
      <?php include_partial('cleverMediaLibrary/form', array('form' => $form, 'cc_media_folder' => $cc_media_folder)); ?>
    </form>
  </div>
</div>