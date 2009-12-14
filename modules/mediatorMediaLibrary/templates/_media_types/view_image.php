<?php
echo image_tag(
  sfConfig::get('app_mediatorMediaLibraryPlugin_media_root', '/media')
  .'/'.mediatorMediaLibraryToolkit::getDirectoryForSize(isset($size) ? $size : 'original')
  .'/'.$mm_media->getmmMediaFolder()->getAbsolutePath()
  .'/'.$mm_media->getThumbnailFilename().'?time='.strtotime($mm_media->getUpdatedAt()),
  $html_options->getRawValue()
);