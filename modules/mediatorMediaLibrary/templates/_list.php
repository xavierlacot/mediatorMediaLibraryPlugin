<?php use_helper('mediatorMediaLibrary'); ?>
<ul class="mediator_media_library_list">
  <?php
  if (!isset($action))
  {
    $action = 'list';
  }

  $node = $mm_media_folder->getRawValue()->getNode();
  if (!$node->isRoot()):
  ?>
    <?php $parent = $node->getParent(); ?>
    <li class="directory parent">
      <span>
        <?php
        echo cml_link_to(
          '<span>'.$parent->getName().'</span>',
          '@mediatorMediaLibrary?action='.$action.'&path='.$parent->getAbsolutePath()
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
          '@mediatorMediaLibrary?action='.$action.'&path='.$directory->getAbsolutePath()
        );
        ?>
      </span>
    </li>
  <?php endforeach; ?>
  <?php foreach ($files as $mm_media): ?>
    <li>
      <span>
        <?php
        include_partial(
          'mediatorMediaLibrary/file_list',
          array('mm_media' => $mm_media, 'mm_media_folder' => $mm_media_folder)
        )
        ?>
      </span>
    </li>
  <?php endforeach; ?>
</ul>