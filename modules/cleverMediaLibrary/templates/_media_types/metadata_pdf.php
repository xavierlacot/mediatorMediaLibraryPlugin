<div class="sf_admin_form_row">
  <?php $pages_count = $cc_media->getMetadata('pages_count'); ?>
  <?php if ($pages_count): ?>
     <?php echo __('%1% page(s)', array('%1%' => $pages_count)) ?>
  <?php endif; ?>
</div>