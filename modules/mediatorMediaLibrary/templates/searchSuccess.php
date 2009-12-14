<?php use_helper('I18N') ?>
<?php use_helper('mediatorMediaLibrary'); ?>
<div id="sf_admin_container" class="mediator-media-library">
  <h1><?php echo __('Media Library') ?></h1>
  <div class="breadcrumb">
    <?php echo __('search') ?> &#62; <?php echo $tag ?>
  </div>

  <div id="mediator_media_library_sidebar">
    <?php include_component('mediatorMediaLibrary', 'search'); ?>
  </div>

  <ul class="mediator_media_library_list">
    <?php foreach ($directories as $directory): ?>
      <li class="directory">
        <span>
          <?php
          echo cml_link_to(
            '<span>'.$directory->getName().'</span>',
            '@mediatorMediaLibrary?action=list&path='.$directory->getAbsolutePath()
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
            array('mm_media' => $mm_media)
          )
          ?>
        </span>
      </li>
    <?php endforeach; ?>
  </ul>
</div>