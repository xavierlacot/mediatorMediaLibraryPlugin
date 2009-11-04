<fieldset id="sf_fieldset_none">
  <legend><?php echo __('General') ?></legend>

  <?php if ($form->hasGlobalErrors()): ?>
    <?php echo $form->renderGlobalErrors() ?>
  <?php endif; ?>

  <div class="sf_admin_form_row sf_admin_text">
    <div>
      <?php echo $form['name']->renderLabel() ?>
      <?php echo $form['name']->render() ?>
      <div class="help"><?php echo $form['name']->renderHelp() ?></div>
    </div>
  </div>

  <?php echo $form['parent']->render() ?>
  <?php echo $form['_csrf_token']->render() ?>

  <?php include_partial('cleverMediaLibrary/folder_form_actions', array('cc_media_folder' => $cc_media_folder)) ?>
</fieldset>