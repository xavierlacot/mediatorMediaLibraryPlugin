<?php use_helper('cleverMediaLibrary'); ?>
<div class="clever_media_library_box" id="clever_media_library_actions">
  <h2><?php echo __('Toolbox') ?></h2>
  <?php $path = $cc_media_folder->getRawValue()->getAbsolutePath(); ?>
  <ul>
    <li>
      <?php
      echo cml_link_to(__('add a new media'), '@cleverMediaLibrary?action=add&path='.$path, array('class' => 'add'));
      ?>
    </li>
    <li>
      <?php
      echo cml_link_to(__('add a subfolder'), '@cleverMediaLibrary?action=folderAdd&path='.$path, array('class' => 'add-folder'));
      ?>
    </li>
    <?php if (!$cc_media_folder->getRawValue()->getNode()->isRoot()): ?>
      <li>
        <?php
        echo cml_link_to(__('rename/move the folder'), '@cleverMediaLibrary?action=folderEdit&path='.$path, array('class' => 'edit'));
        ?>
      </li>
      <li>
        <?php
        echo cml_link_to(
          __('delete this folder'),
          '@cleverMediaLibrary?action=folderDelete&path='.$path,
          array(
            'class' => 'remove',
            'confirm' => __('Are you sure that you want to remove this folder?'),
          )
        );
        ?>
      </li>
    <?php endif; ?>
  </ul>
</div>