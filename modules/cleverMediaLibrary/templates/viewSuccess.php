<?php
use_helper('I18N');
use_helper('cleverMediaLibrary');
use_helper('Date')
?>
<div id="sf_admin_container" class="clever-media-library">
  <h1><?php echo __('View file "%1%"', array('%1%' => $cc_media->getTitle())); ?></h1>
  <?php include_component('cleverMediaLibrary', 'breadcrumb', array('cc_media' => $cc_media)); ?>

  <div id="clever_media_library_sidebar">
    <?php include_partial('cleverMediaLibrary/view_actions', array('cc_media' => $cc_media)); ?>
    <?php include_component('cleverMediaLibrary', 'view_tags', array('cc_media' => $cc_media)); ?>
  </div>

  <?php include_partial('cleverMediaLibrary/flash'); ?>

  <div id="clever_media_library_file_details">
    <div class="sf_admin_form_row" style="clear: none; padding-left: 0;">
      <?php echo $cc_media->getRawValue()->getDisplay(array('size' => 'medium')); ?>
    </div>

    <div class="sf_admin_form_row">
      <?php
      echo __('File size: %1%',
              array('%1%' => cml_format_filesize($cc_media->getFilesize())));
      ?>
    </div>

    <?php $creator = Doctrine::getTable('sfGuardUser')->find($cc_media->getCreatedBy()); ?>
    <?php if (!is_null($creator)): ?>
      <div class="sf_admin_form_row">
        <?php
        echo __('Created at %1% by %2%',
                array('%1%' => format_datetime($cc_media->getCreatedAt()),
                      '%2%' => $creator->getUsername()));
        ?>
      </div>
    <?php endif; ?>

    <?php $updater = Doctrine::getTable('sfGuardUser')->find($cc_media->getUpdatedBy()); ?>
    <?php if (!is_null($updater)): ?>
      <div class="sf_admin_form_row">
        <?php
        echo __('Updated at %1% by %2%',
                array('%1%' => format_datetime($cc_media->getUpdatedAt()),
                      '%2%' => $updater->getUsername()));
        ?>
      </div>
    <?php endif; ?>

    <?php try { ?>
      <?php
      include_partial('cleverMediaLibrary/media_types/metadata'.(('' != $cc_media->getType()) ? '_'.$cc_media->getType() : ''),
        array('cc_media' => $cc_media,)
      );
      ?>
    <?php } catch (Exception $e) { } ?>

    <div class="sf_admin_form_row">
      <?php
      echo $form['file']->renderLabel();
      echo $form['file']->render();
      ?>
    </div>

    <div class="sf_admin_form_row">
      <?php
      echo $form['body']->renderLabel();
      echo $form['body']->render();
      ?>
    </div>
  </div>
</div>