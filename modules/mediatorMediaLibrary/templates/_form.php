<?php use_helper('mediatorMediaLibrary'); ?>
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

  <?php echo $form['mm_media_folder_id']->render() ?>
  <?php echo $form['_csrf_token']->render() ?>

  <?php include_partial('mediatorMediaLibrary/form_actions', array('mm_media_folder' => $mm_media_folder)) ?>
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
    $('#mm_media_file').uploadifyUpload();
    return false;
  });

  $('.sf_admin_form_row label').hide();

  var errors = new Array();

  $('#mm_media_file').uploadify({
    'fileDataName':  'mm_media[file]',
    'wmode':      'transparent',
    'simUploadLimit': 3,
    'uploader':   '/mediatorMediaLibraryPlugin/js/jquery.uploadify/uploadify.swf',
    'script':     $('#sf_admin_container form').attr('action'),
    'cancelImg':  '/mediatorMediaLibraryPlugin/images/cancel.png',
    'multi':      true,
    'sizeLimit':  '<?php echo $max_size ?>',
    'onError': function(e, q, f, err) {
      errors.push(f.name);
    },
    'onAllComplete': function(e, d) {
      if (d.filesUploaded > 0) {
        if (d.errors == 0) {
          $('#sf_admin_container form').html('<p><?php echo str_replace('\'', '\\\'', __('The upload is completed. May be would you like to %1%?', array('%1%' => cml_link_to(__('see the files'), '@mediatorMediaLibrary?action=list&path='.$mm_media_folder->getAbsolutePath())))) ?></p>');
        } else {
          $('#sf_admin_container form').html('<p><?php echo str_replace('%1%', '\' + errors.join(", ") + \'', str_replace('\'', '\\\'', __('The upload is completed, but some files (%1%) have not been uploaded. May be would you like to %2%?', array('%2%' => cml_link_to(__('see the uploaded files'), '@mediatorMediaLibrary?action=list&path='.$mm_media_folder->getAbsolutePath()))))) ?></p>');
        }
      }
    },
    'scriptData': {
      '<?php echo session_name(); ?>': '<?php echo session_id(); ?>',
      'mm_media[_csrf_token]': $('#mm_media__csrf_token').val(),
      'mm_media[mm_media_folder_id]': $('#mm_media_mm_media_folder_id').val()
    }
  });
});

</script>