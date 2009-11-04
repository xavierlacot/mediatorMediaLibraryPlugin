<?php
echo image_tag(
  sfConfig::get('app_cleverMediaLibraryPlugin_media_root', '/media')
  .'/'.cleverMediaLibraryToolkit::getDirectoryForSize(isset($size) ? $size : 'original')
  .'/'.$cc_media->getCcMediaFolder()->getAbsolutePath()
  .'/'.$cc_media->getThumbnailFilename().'?time='.strtotime($cc_media->getUpdatedAt()),
  $html_options->getRawValue()
);