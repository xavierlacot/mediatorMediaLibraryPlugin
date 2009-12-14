<?php
use_helper('I18N');
use_helper('mediatorMediaLibrary');
use_helper('Date')
?>
<div id="sf_admin_container" class="mediator-media-library">
  <h1><?php echo __('View file "%1%"', array('%1%' => $mm_media->getTitle())); ?></h1>
  <?php include_component('mediatorMediaLibrary', 'breadcrumb', array('mm_media' => $mm_media)); ?>

  <div id="mediator_media_library_sidebar">
    <?php include_partial('mediatorMediaLibrary/view_actions', array('mm_media' => $mm_media)); ?>
    <?php include_component('mediatorMediaLibrary', 'view_tags', array('mm_media' => $mm_media)); ?>
  </div>

  <?php include_partial('mediatorMediaLibrary/flash'); ?>

  <div id="mediator_media_library_file_details">
    <div class="sf_admin_form_row" style="clear: none; padding-left: 0;">
      <?php echo $mm_media->getRawValue()->getDisplay(array('size' => 'medium')); ?>
    </div>

    <div class="sf_admin_form_row">
      <?php
      echo __('File size: %1%',
              array('%1%' => cml_format_filesize($mm_media->getFilesize())));
      ?>
    </div>

    <?php $creator = Doctrine::getTable('sfGuardUser')->find($mm_media->getCreatedBy()); ?>
    <?php if (!is_null($creator)): ?>
      <div class="sf_admin_form_row">
        <?php
        echo __('Created at %1% by %2%',
                array('%1%' => format_datetime($mm_media->getCreatedAt()),
                      '%2%' => $creator->getUsername()));
        ?>
      </div>
    <?php endif; ?>

    <?php $updater = Doctrine::getTable('sfGuardUser')->find($mm_media->getUpdatedBy()); ?>
    <?php if (!is_null($updater)): ?>
      <div class="sf_admin_form_row">
        <?php
        echo __('Updated at %1% by %2%',
                array('%1%' => format_datetime($mm_media->getUpdatedAt()),
                      '%2%' => $updater->getUsername()));
        ?>
      </div>
    <?php endif; ?>

    <?php try { ?>
      <?php
      include_partial('mediatorMediaLibrary/media_types/metadata'.(('' != $mm_media->getType()) ? '_'.$mm_media->getType() : ''),
        array('mm_media' => $mm_media,)
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