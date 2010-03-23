<?php use_helper('I18N') ?>
<div id="sf_admin_container" class="mediator-media-library">
  <h1><?php echo __('Move media "%1%"', array('%1%' => $mm_media->getTitle())) ?></h1>
  <?php include_component('mediatorMediaLibrary', 'breadcrumb', array('mm_media' => $mm_media)); ?>

  <div id="mediator_media_library_sidebar">
    <?php include_partial('mediatorMediaLibrary/view_actions', array('mm_media' => $mm_media)); ?>
  </div>

  <?php include_partial('mediatorMediaLibrary/flash'); ?>

  <form action="<?php echo url_for('mediatorMediaLibrary/move?path='.$mm_media->getAbsoluteFilename()) ?>" method="post" id="mediator_media_library_media_edit_form">
    <div id="mediator_media_library_file_details">
      <div class="sf_admin_form_row">
        <?php
        echo $form['mm_media_folder_id']->renderLabel();
        echo $form['mm_media_folder_id']->render();
        ?>
      </div>
      <div class="sf_admin_form_row">
        <?php
        echo $form->renderHiddenFields();
        ?>
        <input type="submit" value="ok" />
      </div>
    </div>
  </form>
</div>