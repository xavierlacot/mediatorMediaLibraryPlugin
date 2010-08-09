<?php use_helper('I18N') ?>
<?php include_partial('mediatorMediaLibrary/assets') ?>
<div id="sf_admin_container" class="mediator-media-library">
  <h1><?php echo __('Title, description and tags') ?></h1>

  <div class="sf_admin_form">
    <form method="post" action="<?php echo url_for('@mediatorMediaLibrary_describe?path='.$uuids) ?>" id="mediator-media-description">
      <?php include_partial('mediatorMediaLibrary/flash'); ?>
      <?php include_partial('mediatorMediaLibrary/description', array('form' => $form, 'autocomplete_url' => $autocomplete_url)); ?>
    </form>
  </div>
</div>

<?php if ($sf_request->isXmlHttpRequest()): // Add this CSS only when there is no layout ?>
  <?php echo javascript_include_tag('/mediatorMediaLibraryPlugin/js/jquery.select.mediatorMediaLibrary.js'); ?>
<?php endif; ?>