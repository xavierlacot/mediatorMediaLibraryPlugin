<?php use_helper('I18N') ?>
<div id="sf_admin_container" class="clever-media-library">
  <h1><?php echo __('Editing folder "%1%"', array('%1%' => $cc_media_folder->getName())) ?></h1>
  <div class="sf_admin_form">
    <?php echo form_tag_for($form, 'cleverMediaLibrary/folderEdit') ?>
      <?php include_partial('cleverMediaLibrary/flash'); ?>
      <?php include_partial('cleverMediaLibrary/folder_form', array('form' => $form, 'cc_media_folder' => $cc_media_folder)); ?>
    </form>
  </div>
</div>