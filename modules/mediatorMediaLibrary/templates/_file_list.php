<?php use_helper('mediatorMediaLibrary'); ?>
<?php $display = $mm_media->getRawValue()->getDisplay(array('size' => 'small')) ?>
<?php if (!isset($mm_media_folder)) $mm_media_folder = $mm_media->getmmMediaFolder() ?>

<?php
echo cml_link_to(
  '<span>'.$mm_media->getTitle().'</span>'.$display,
  '@mediatorMediaLibrary?action=view&path='.$mm_media->getAbsoluteFilename($mm_media_folder),
  array('class' => 'file '.$mm_media->getType())
);