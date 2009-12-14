<?php use_helper('mediatorMediaLibrary'); ?>
<input type="hidden" name="mm_media_librart_widget_fieldname" id="mm_media_librart_widget_fieldname" value="<?php echo $sf_request->getParameter('fieldname') ?>" />

<ul class="mediator_media_library_list">
  <?php if (!$mm_media_folder->getRawValue()->getNode()->isRoot()): ?>
    <li class="directory parent">
      <span>
        <?php
        echo cml_link_to('<span>'.$mm_media_folder->getRawValue()->getNode()->getParent()->getName().'</span>',
                     '@mediatorMediaLibrary?action=chooseImage&path='.$mm_media_folder->getRawValue()->getNode()->getParent()->getAbsolutePath(),
                     array('rel' => 'facebox'));
        ?>
      </span>
    </li>
  <?php endif; ?>
  <?php foreach ($directories as $directory): ?>
    <li class="directory">
      <span>
        <?php
        echo cml_link_to('<span>'.$directory->getRawValue()->getName().'</span>',
                     '@mediatorMediaLibrary?action=chooseImage&path='.$directory->getRawValue()->getAbsolutePath(),
                     array('rel' => 'facebox'));
        ?>
      </span>
    </li>
  <?php endforeach; ?>
  <?php foreach ($files as $mm_media): ?>
    <li>
      <span>
        <?php if ($mm_media->getType() != ''): ?>
          <?php
          echo cml_link_to('<span>'.$mm_media->getTitle().'</span>'.image_tag(sfConfig::get('app_mediatorMediaLibraryPlugin_media_root', 'media')
                                 .'/'.mediatorMediaLibraryToolkit::getDirectoryForSize('small')
                                 .'/'.$mm_media->getmmMediaFolder()->getAbsolutePath()
                                 .'/'.$mm_media->getThumbnailFilename()),
                       '@mediatorMediaLibrary?action=view&path='.$mm_media->getAbsoluteFilename(),
                       array(
                         'class' => 'file '.$mm_media->getType(),
                         'id'    => $mm_media->getPrimaryKey()
                      ));
          ?>
        <?php else: ?>
          <?php
          echo cml_link_to('<span>'.$mm_media->getTitle().'</span>',
                       '@mediatorMediaLibrary?action=view&path='.$mm_media->getAbsoluteFilename(),
                       array(
                         'class' => 'file '.$mm_media->getType(),
                         'id'    => $mm_media->getPrimaryKey()
                       ));
          ?>
        <?php endif; ?>
      </span>
    </li>
  <?php endforeach; ?>
</ul>