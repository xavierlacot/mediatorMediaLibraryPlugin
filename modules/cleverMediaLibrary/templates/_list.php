<?php use_helper('cleverMediaLibrary'); ?>
<ul class="clever_media_library_list">
  <?php
  $node = $cc_media_folder->getRawValue()->getNode();
  if (!$node->isRoot()):
  ?>
    <?php $parent = $node->getParent(); ?>
    <li class="directory parent">
      <span>
        <?php
        echo cml_link_to(
          '<span>'.$parent->getName().'</span>',
          '@cleverMediaLibrary?action=list&path='.$parent->getAbsolutePath()
        );
        ?>
      </span>
    </li>
  <?php endif;?>
  <?php foreach ($directories as $directory): ?>
    <li class="directory">
      <span>
        <?php
        echo cml_link_to(
          '<span>'.$directory->getName().'</span>',
          '@cleverMediaLibrary?action=list&path='.$directory->getAbsolutePath()
        );
        ?>
      </span>
    </li>
  <?php endforeach; ?>
  <?php foreach ($files as $cc_media): ?>
    <li>
      <span>
        <?php
        include_partial(
          'cleverMediaLibrary/file_list',
          array('cc_media' => $cc_media, 'cc_media_folder' => $cc_media_folder)
        )
        ?>
      </span>
    </li>
  <?php endforeach; ?>
</ul>