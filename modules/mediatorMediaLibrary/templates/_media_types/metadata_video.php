<div class="sf_admin_form_row">
  <?php
  echo __(
    'Size: %1%px x %2%px',
    array(
      '%1%' => $mm_media->getWidth(),
      '%2%' => $mm_media->getWidth()
    )
  );
  ?>
</div>
<?php $metadatas = $mm_media->getMetadatas(); ?>
<?php foreach ($metadatas as $metadata): ?>
  <div class="sf_admin_form_row">
    <?php
    echo __(
      '%1%: %2%',
      array(
        '%1%' => __($metadata->getName()),
        '%2%' => $metadata->getValue()
      )
    );
    ?>
  </div>
<?php endforeach; ?>