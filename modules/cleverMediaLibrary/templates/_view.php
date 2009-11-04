<div class="sf_admin_form_row" style="clear: none">
  <?php
  echo image_tag('/'.sfConfig::get('app_cleverMediaLibraryPlugin_media_root', 'media')
                   .'/'.cleverMediaLibraryToolkit::getDirectoryForSize('thumbnails')
                   .'/'.$cc_media->getThumbnailFilename(),
                 array('alt' => $cc_media->getTitle()));
  ?>
</div>