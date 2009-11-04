<div class="sf_admin_form_row">
  <?php
  echo __(
    'Image size: %1%px x %2%px',
    array(
      '%1%' => $cc_media->getMetadata('width'),
      '%2%' => $cc_media->getMetadata('height')
    )
  );
  ?>
</div>
<?php $metadatas = $cc_media->getMetadatas(); ?>
<?php foreach ($metadatas as $metadata): ?>
  <?php if (in_array($metadata->getName(), sfConfig::get('app_cleverMediaLibraryPlugin_metadata_exif', array()))): ?>
    <div class="sf_admin_form_row">
      <?php
      echo __(
        '%1%: %2%',
        array(
          '%1%' => $metadata->getName(),
          '%2%' => $metadata->getValue()
        )
      );
      ?>
    </div>
  <?php endif; ?>
<?php endforeach; ?>