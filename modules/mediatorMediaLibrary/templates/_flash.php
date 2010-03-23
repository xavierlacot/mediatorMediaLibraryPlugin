<?php if ($sf_user->hasFlash('notice')): ?>
  <div class="flash_notice"><?php echo __($sf_user->getFlash('notice')) ?></div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error')): ?>
  <div class="flash_error"><?php echo __($sf_user->getFlash('error')) ?></div>
<?php endif; ?>