<?php use_helper('mediatorMediaLibrary'); ?>
<div class="mediator_media_library_box" id="mediator_media_library_actions">
  <h2><?php echo __('Toolbox') ?></h2>
  <?php $mm_media_folder_absolute_path = $mm_media->getmmMediaFolder()->getAbsolutePath(); ?>
  <?php $path = $mm_media_folder_absolute_path.DIRECTORY_SEPARATOR.$mm_media->getFilename(); ?>
  <ul>
    <li>
      <?php
      echo link_to(
        __('go to the parent folder'),
        '@mediatorMediaLibrary?action=list&path='.$mm_media_folder_absolute_path,
        array('class' => 'folder')
      )
      ?>
    </li>
    <li>
      <?php
      echo link_to(
        __('download media'),
        $mm_media->getUrl(array('size' => 'original')),
        array('class' => 'download'));
      ?>
    </li>
    <li>
      <?php
      echo cml_link_to(
        __('move this media'),
        '@mediatorMediaLibrary?action=move&path='.$path,
        array('class'   => 'move')
      );
      ?>
    </li>
    <?php if (!is_null($mm_media->getType())): ?>
      <li>
        <?php
        echo cml_link_to(
          __('edit media'),
          '@mediatorMediaLibrary?action=edit&path='.$path,
          array('class' => 'edit')
        )
        ?>
      </li>
    <?php endif; ?>
    <li>
      <?php
      echo cml_link_to(
        __('delete this media'),
        '@mediatorMediaLibrary?action=delete&path='.$path,
        array(
          'class'   => 'remove',
          'onclick' => 'if (!confirm("'.__('Do you really want to delete the file \"%1%\"?', array('%1%' => $mm_media->getRawValue()->getFilename())).'")) { return false }'
        )
      );
      ?>
    </li>
  </ul>
</div>