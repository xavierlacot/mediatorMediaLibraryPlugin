<?php use_helper('cleverMediaLibrary'); ?>
<div class="breadcrumb">
  <?php foreach ($path as $node): ?>
    <?php echo cml_link_to($node->getName(), 'cleverMediaLibrary/list?path='.$node->getAbsolutePath()).' > '; ?>
  <?php endforeach; ?>
  <?php
  echo cml_link_to(
    $cc_media->getRawValue()->getTitle(),
    'cleverMediaLibrary/view?path='.$cc_media->getRawValue()->getAbsoluteFilename()
  );
  ?>
</div>