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
<?php if ($form->isNew() === false): ?>
  <?php echo $form['id']->render() ?>
  <?php echo $form['auto_path']->render() ?>
  <?php echo $form['folder_path']->render() ?>
<?php endif; ?>
  <?php echo $form['parent']->render() ?>
  <?php echo $form['_csrf_token']->render() ?>

  <?php include_partial('mediatorMediaLibrary/folder_form_actions', array('mm_media_folder' => $mm_media_folder)) ?>
</fieldset>