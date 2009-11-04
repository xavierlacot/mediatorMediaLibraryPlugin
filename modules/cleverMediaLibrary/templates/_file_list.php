<?php use_helper('cleverMediaLibrary'); ?>
<?php $display = $cc_media->getRawValue()->getDisplay(array('size' => 'small')) ?>
<?php if (!isset($cc_media_folder)) $cc_media_folder = $cc_media->getCcMediaFolder() ?>

<?php
echo cml_link_to(
  '<span>'.$cc_media->getTitle().'</span>'.$display,
  '@cleverMediaLibrary?action=view&path='.$cc_media->getAbsoluteFilename($cc_media_folder),
  array('class' => 'file '.$cc_media->getType())
);