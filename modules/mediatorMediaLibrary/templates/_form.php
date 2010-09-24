<?php use_helper('mediatorMediaLibrary'); ?>

<?php include_javascripts_for_form($form); ?>
<?php include_stylesheets_for_form($form); ?>

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

  <?php echo $form->renderHiddenFields() ?>

  <?php include_partial('mediatorMediaLibrary/form_actions', array('mm_media_folder' => $mm_media_folder)) ?>
</fieldset>

<script type="text/javascript">
$(document).ready(function() {
  $('form#mediator-media-add-form').bind('submit', function() {
    $('#mm_media_file').uploadifyUpload();
    return false;
  });

  if (!swfobject.hasFlashPlayerVersion("9.0.24")) {
    $('#mm_media_file').parent().prepend("<p style='margin-bottom:20px!important;' class='error'><?php echo __('You are using an obsolete version of the flash plugin. Please update it or deactivate it.') ?></p>");
  } else {
    $('#mediator-media-add-form label').hide();
    var hash_keys = new Array();
    var errors = new Array();
    $('#mm_media_file').uploadify({
      'fileDataName':    'mm_media[file]',
      'wmode':           'transparent',
      'buttonText':      '<?php echo __('Browse'); ?>',
      'simUploadLimit':  <?php echo sfConfig::get('app_mediatorMediaLibraryPlugin_upload_simultaneaous', 3) ?>,
      'uploader':        '/mediatorMediaLibraryPlugin/js/jquery.uploadify/uploadify.swf',
      'script':          $('#mediator-media-add form').attr('action'),
      'cancelImg':       '/mediatorMediaLibraryPlugin/images/cancel.png',
      'multi':           true,
      <?php if (isset($fileDesc)): ?>
        'fileDesc':        '<?php echo $fileDesc; ?>',
      <?php endif; ?>
      <?php if (isset($fileExt)): ?>
        'fileExt':         '<?php echo $fileExt; ?>',
      <?php endif; ?>
      'sizeLimit':       '<<?php echo mediatorMediaLibraryToolkit::getMaxAllowedFilesize() ?>>',
      'onComplete':  function(e, q, f, r, d) { // event, ID, fileObj, response, data
        hash_keys.push(r);
        return true;
      },
      'onError': function (e, q, f, r) {
        alert(r.type + ' : ' + r.info);
      },
      'onAllComplete':  function(e, d) {
        if (d.errors > 0) {
          $('#mediator-media-add form input').hide();
          $('#mediator-media-add form object').hide();

          if (d.filesUploaded > 0) {
            // there is a file OK, other in errors...
            $('#mediator-media-add form').after("<p class=\"error\"><?php echo __('The upload is completed for some files, but some files could not be uploaded.') ?> <a class=\"btn-like\" href=\"<?php echo url_for('@mediatorMediaLibrary_describe?media_ids=') ?>" + hash_keys.join(',') + "\"><?php echo __('Go to the description of the uploaded files') ?></a>.</p>");
          } else {
            // all files have failed
            $('#mediator-media-add form').after("<p class=\"error\"><?php echo __('A problem happened, and no file was uploaded.') ?></p>");
          }
        } else {
          // Upload complete with no error, go to the next step
          $('#mediator-media-add form').hide().after('<p><?php echo image_tag('icons/load.gif'); ?></p>');

          // load the description page
          var destination = $('#facebox .content');
          var path = '<?php echo url_for('@mediatorMediaLibrary_describe?media_ids=') ?>' + hash_keys.join(',') + ',nocache=' + new Date().getTime();

          if (destination.length == 0) {
            $(location).attr('href', path);
          }

          destination.load(
            path,
            function(r, s) { // response, status
              if (s == 'error') {
                $('#mediator-media-add').html("<p class=\"error\"><?php echo __('A problem happened, and no file was uploaded.') ?></p>");
              } else {
                //console.log('call rebind media');
                $('html').trigger('mediatormediarebind');
                //console.log('called rebind media');
              }
            }
          );
        }

        // Ajax load for links
        $('#mediator-media-add a:not(.file)').click(function() {
          $('#mediator-media-add').load(this.href, null, function() { $('html').trigger('mediatormediarebind'); });
          return false;
        });

        return true;
      },
      'scriptData': {
        '<?php echo session_name(); ?>': '<?php echo session_id(); ?>',
        'mm_media[_csrf_token]': $('#mm_media__csrf_token').val(),
        'mm_media[mm_media_folder_id]': $('#mm_media_mm_media_folder_id').val(),
        'nocache': new Date().getTime()
      }
    });
  }
});

</script>