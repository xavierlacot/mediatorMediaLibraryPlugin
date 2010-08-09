<?php
use_helper('mediatorMediaLibrary');

if (!isset($action))
{
  $action = 'list';
}
?>
<div class="breadcrumb">
  <?php foreach ($path as $node): ?>
    <?php echo cml_link_to($node->getName(), 'mediatorMediaLibrary/'.$action.'?path='.$node->getAbsolutePath()); ?>
    <?php if ($node->getId() != $mm_media_folder->getId()): ?>
      &#62;
    <?php endif; ?>
  <?php endforeach; ?>
</div>