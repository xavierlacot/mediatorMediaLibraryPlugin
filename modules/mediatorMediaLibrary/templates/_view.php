<div class="sf_admin_form_row" style="clear: none">
  <?php
  echo image_tag('/'.sfConfig::get('app_mediatorMediaLibraryPlugin_media_root', 'media')
                   .'/'.mediatorMediaLibraryToolkit::getDirectoryForSize('thumbnails')
                   .'/'.$mm_media->getThumbnailFilename(),
                 array('alt' => $mm_media->getTitle()));
  ?>
</div>