<?php use_helper('mediatorMediaLibrary'); ?>
<div class="mediator_media_library_box" id="mediator_media_library_actions">
  <h2><?php echo __('Toolbox') ?></h2>
  <?php $path = $mm_media_folder->getRawValue()->getAbsolutePath(); ?>
  <ul>
    <li>
      <?php
      echo cml_link_to(__('add a new media'), '@mediatorMediaLibrary?action=add&path='.$path, array('class' => 'add'));
      ?>
    </li>
    <li>
      <?php
      echo cml_link_to(__('add a subfolder'), '@mediatorMediaLibrary?action=folderAdd&path='.$path, array('class' => 'add-folder'));
      ?>
    </li>
    <?php if (!$mm_media_folder->getRawValue()->getNode()->isRoot()): ?>
      <li>
        <?php
        echo cml_link_to(__('rename/move the folder'), '@mediatorMediaLibrary?action=folderEdit&path='.$path, array('class' => 'edit'));
        ?>
      </li>
      <li>
        <?php
        echo cml_link_to(
          __('delete this folder'),
          '@mediatorMediaLibrary?action=folderDelete&path='.$path,
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