<?php use_helper('cleverMediaLibrary'); ?>
<div class="clever_media_library_box" id="clever_media_library_actions">
  <h2><?php echo __('Toolbox') ?></h2>
  <?php $cc_folder_absolute_path = $cc_media->getCcMediaFolder()->getAbsolutePath(); ?>
  <?php $path = $cc_folder_absolute_path.DIRECTORY_SEPARATOR.$cc_media->getFilename(); ?>
  <ul>
    <li>
      <?php
      echo link_to(__('go to the parent folder'), '@cleverMediaLibrary?action=list&path='.$cc_folder_absolute_path, array('class' => 'folder'))
      ?>
    </li>
    <li>
      <?php
      echo link_to(__('download media'),
                   '/'.sfConfig::get('app_cleverMediaLibraryPlugin_media_root', 'media')
                  .'/'.cleverMediaLibraryToolkit::getDirectoryForSize('original')
                  .'/'.$path,
                   array('class' => 'download'))
      ?>
    </li>
    <?php if (!is_null($cc_media->getType())): ?>
      <li>
        <?php
        echo cml_link_to(__('edit media'), '@cleverMediaLibrary?action=edit&path='.$path, array('class' => 'edit'))
        ?>
      </li>
    <?php endif; ?>
    <li>
      <?php
      echo cml_link_to(
             __('delete this media'),
             '@cleverMediaLibrary?action=delete&path='.$path,
             array(
               'class'   => 'remove',
               'onclick' => 'if (!confirm("'.__('Do you really want to delete the file \"%1%\"?', array('%1%' => $cc_media->getRawValue()->getFilename())).'")) { return false }'
             )
           );
      ?>
    </li>
  </ul>
</div>