<?php use_helper('mediatorMediaLibrary'); ?>
<div class="breadcrumb">
  <?php foreach ($path as $node): ?>
    <?php echo cml_link_to($node->getName(), 'mediatorMediaLibrary/list?path='.$node->getAbsolutePath()).' > '; ?>
  <?php endforeach; ?>
  <?php
  echo cml_link_to(
    $mm_media->getRawValue()->getTitle(),
    'mediatorMediaLibrary/view?path='.$mm_media->getRawValue()->getAbsoluteFilename()
  );
  ?>
</div>