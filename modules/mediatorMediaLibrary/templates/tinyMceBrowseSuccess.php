<?php use_helper('cleverMediaLibrary'); ?>

<div class="popup">
  <h1><?php echo __('Media Library') ?></h1>
  <?php  include_component('cleverMediaLibrary', 'folder_breadcrumb', array('cc_media_folder' => $cc_media_folder)); ?>

  <ul class="clever_media_library_list">
    <?php if (!$cc_media_folder->getRawValue()->getNode()->isRoot()): ?>
      <li class="directory parent">
        <span>
          <?php
          $parent = $cc_media_folder->getRawValue()->getNode()->getParent();
          echo cml_link_to(
            '<span>'.$parent->getName().'</span>',
            '@cleverMediaLibrary?action=tinyMceBrowse&path='.$parent->getAbsolutePath()
          );
          ?>
        </span>
      </li>
    <?php endif; ?>
    <?php foreach ($directories as $directory): ?>
      <li class="directory">
        <span>
          <?php
          echo cml_link_to(
            '<span>'.$directory->getRawValue()->getName().'</span>',
            '@cleverMediaLibrary?action=tinyMceBrowse&path='.$directory->getRawValue()->getAbsolutePath()
          );
          ?>
        </span>
      </li>
    <?php endforeach; ?>
    <?php foreach ($files as $cc_media): ?>
      <li>
        <span>
          <a
            href=""
            class="file <?php echo $cc_media->getType() ?>"
            id="media-<?php echo $cc_media->getPrimaryKey() ?>"
            title="<?php echo __('Add to TinyMCE') ?>"
            onclick="cleverMediaLibrary.doInsertImage(
              '<?php echo $cc_media->getUrl(array('size' => 'original')) ?>',
              {
                'id': <?php echo $cc_media->getId() ?>,
                'uri': '<?php echo $cc_media->getUrl(array('size' => 'original')) ?>',
                'alt': '<?php echo $cc_media->getBody() ?>'
              }); return false;">
              <?php echo '<span>'.$cc_media->getTitle().'</span>'.$cc_media->getRawValue()->getDisplay(array('size' => 'small')) ?>
          </a>
        </span>
      </li>
    <?php endforeach; ?>
  </ul>
</div>