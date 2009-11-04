<?php use_helper('cleverMediaLibrary'); ?>
<div class="breadcrumb">
  <?php foreach ($path as $node): ?>
    <?php echo cml_link_to($node->getName(), 'cleverMediaLibrary/list?path='.$node->getAbsolutePath()); ?>
    <?php if ($node->getId() != $cc_media_folder->getId()): ?>
      &#62;
    <?php endif; ?>
  <?php endforeach; ?>
</div>