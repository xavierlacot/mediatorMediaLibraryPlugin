<?php use_helper('I18N') ?>
<?php use_helper('cleverMediaLibrary'); ?>
<div id="sf_admin_container" class="clever-media-library">
  <h1><?php echo __('Media Library') ?></h1>
  <div class="breadcrumb">
    <?php echo __('search') ?> &#62; <?php echo $tag ?>
  </div>

  <div id="clever_media_library_sidebar">
    <?php include_component('cleverMediaLibrary', 'search'); ?>
  </div>

  <ul class="clever_media_library_list">
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
            array('cc_media' => $cc_media)
          )
          ?>
        </span>
      </li>
    <?php endforeach; ?>
  </ul>
</div>