<?php use_helper('cleverMediaLibrary'); ?>
<?php foreach ($form->getJavascripts() as $javascript): ?>
  <?php use_javascript($javascript)?>
<?php endforeach?>

<?php foreach ($form->getStylesheets() as $stylesheet): ?>
  <?php use_stylesheet($stylesheet)?>
<?php endforeach?>

<fieldset id="sf_fieldset_none">
  <legend><?php echo __('General') ?></legend>

  <?php if ($form->hasGlobalErrors()): ?>
    <?php echo $form->renderGlobalErrors() ?>
  <?php endif; ?>

  <div class="sf_admin_form_row sf_admin_text">
    <div>
      <?php echo $form['file']->renderLabel() ?>
      <?php echo $form['file']->render() ?>
      <div class="help"><?php echo $form['file']->renderHelp() ?></div>
    </div>
  </div>

  <?php echo $form['cc_media_folder_id']->render() ?>
  <?php echo $form['_csrf_token']->render() ?>

  <?php include_partial('cleverMediaLibrary/form_actions', array('cc_media_folder' => $cc_media_folder)) ?>
</fieldset>

<?php
$max_size = ini_get('upload_max_filesize');

if (preg_match('#^([0-9]+?)([gmk])$#i', $max_size, $tokens))
{
  $size_val = isset($tokens[1]) ? $tokens[1] : null;
  $unit     = isset($tokens[2]) ? $tokens[2] : null;

  if ($size_val && $unit)
  {
    switch (strtolower($unit))
    {
      case 'g':
        $max_size = $size_val * 1024 * 1024 * 1024;
        break;
      case 'm':
        $max_size = $size_val * 1024 * 1024;
        break;
      case 'k':
        $max_size = $size_val * 1024;
        break;
    }
  }
}
?>

<script type="text/javascript">


$(document).ready(function() {
  $('#sf_admin_container form').bind('submit', function() {
    $('#cc_media_file').uploadifyUpload();
    return false;
  });

  $('.sf_admin_form_row label').hide();

  $('#cc_media_file').uploadify({
    'fileDataName':  'cc_media[file]',
    'wmode':      'transparent',
    'simUploadLimit': 3,
    'uploader':   '/mediatorPlugin/js/jquery.uploadify/uploadify.swf',
    'script':     $('#sf_admin_container form').attr('action'),
    'cancelImg':  '/mediatorPlugin/images/cancel.png',
    'multi':      true,
    'sizeLimit':  '<?php echo $max_size ?>',
    'onAllComplete':  function(e, d) {
      $('#sf_admin_container form').html('<p><?php echo __('The upload is completed. May be would you like to %1%?', array('%1%' => cml_link_to(__('see the files'), '@cleverMediaLibrary?action=list&path='.$cc_media_folder->getAbsolutePath()))) ?></p>')
    },
    'scriptData': {
      '<?php echo session_name(); ?>': '<?php echo session_id(); ?>',
      'cc_media[_csrf_token]': $('#cc_media__csrf_token').val(),
      'cc_media[cc_media_folder_id]': $('#cc_media_cc_media_folder_id').val()
    }
  });
});

</script>