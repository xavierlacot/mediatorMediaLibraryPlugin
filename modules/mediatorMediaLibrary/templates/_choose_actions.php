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
  </ul>
</div>