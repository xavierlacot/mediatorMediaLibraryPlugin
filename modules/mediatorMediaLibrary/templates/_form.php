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

<div id="mediator-uploader">
  <p>
    <a href="#" id="mediator-browse"><?php echo __('Browse')?></a> |
    <a href="#" id="mediator-clear"><?php echo __('Clear file list') ?></a> |
    <a href="#" id="mediator-upload"><?php echo __('Start upload') ?></a>
  </p>
  <div>
    <strong class="overall-title"></strong><br />
      <img src="/mediatorMediaLibraryPlugin/images/fancyupload/assets/progress-bar/bar.gif" class="progress overall-progress" />
  </div>
  <div>
    <strong class="current-title"></strong><br />
    <img src="/mediatorMediaLibraryPlugin/images/fancyupload/assets/progress-bar/bar.gif" class="progress current-progress" />
  </div>
  <div class="current-text"></div>
</div>

<ul id="mediator-uploader-file-list"></ul>

<script type="text/javascript">
var $ = jQuery;
var moo = document.id();

$(document).ready(function() {
  $('#sf_fieldset_none').hide();

  // hash keys of the medias
  var hash_keys = new Array();
  var errors = new Array();

  // our uploader instance
  var up = new FancyUpload2(document.id('mediator-uploader'), document.id('mediator-uploader-file-list'), { // options object
    // we console.log infos, remove that in production!!
    verbose: false,
    timeLimit: 14400,
    fieldName: 'mm_media[file]',
    // url is read from the form, so you just have to change one place
    url: document.id('mediator-media-add-form').action,
    data: { '<?php echo session_name(); ?>':'<?php echo session_id(); ?>',
            'nocache': new Date().getTime(),
            'mm_media[_csrf_token]':document.id('mm_media__csrf_token').get('value'),
            'mm_media[mm_media_folder_id]':document.id('mm_media_mm_media_folder_id').get('value')
           },
    // path to the SWF file
    path: '/mediatorMediaLibraryPlugin/js/fancyupload/Swiff.Uploader.swf',

    // this is our browse button, *target* is overlayed with the Flash movie
    target: 'mediator-browse',

    // graceful degradation, onLoad is only called if all went well with Flash
    onLoad: function() {

      // We relay the interactions with the overlayed flash to the link
      this.target.addEvents({
        click: function() {
          return false;
        },
        mouseenter: function() {
          this.addClass('hover');
        },
        mouseleave: function() {
          this.removeClass('hover');
          this.blur();
        },
        mousedown: function() {
          this.focus();
        }
      });

      // Interactions for the 2 other buttons

      document.id('mediator-clear').addEvent('click', function() {
        up.remove(); // remove all files
        return false;
      });

      document.id('mediator-upload').addEvent('click', function() {
        up.start(); // start upload
        return false;
      });
    },

    // Edit the following lines, it is your custom event handling

    /**
     * Is called when files were not added, "files" is an array of invalid File classes.
     *
     * This example creates a list of error elements directly in the file list, which
     * hide on click.
     */
    onSelectFail: function(files) {
      files.each(function(file) {
        new Element('li', {
          'class': 'validation-error',
          html: file.validationErrorMessage || file.validationError,
          title: MooTools.lang.get('FancyUpload', 'removeTitle'),
          events: {
            click: function() {
              this.destroy();
            }
          }
        }).inject(this.list, 'top');
      }, this);
    },

    onFileSuccess: function(file, response) {
      if (response) {
        hash_keys.push(response);
      }
    },

    onFileError: function(file) {
        console.log('onFileError');
        console.log(file);
    },

    onFileComplete: function(file) {
    	if (file.response.error) {
        errors.push(this.fileList[0].name);
    	  console.log('Failed Upload', 'Uploading <em>' + this.fileList[0].name + '</em> failed, please try again. (Error: #' + this.fileList[0].response.code + ' ' + this.fileList[0].response.error + ')');
      }
    },

    onFail: function(error) {
      switch (error) {
        case 'hidden': // works after enabling the movie and clicking refresh
          alert('<?php echo __('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).'); ?>');
          break;
        case 'blocked': // This no *full* fail, it works after the user clicks the button
          alert('<?php echo __('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).'); ?>');
          break;
        case 'empty':
          alert('<?php echo __('A required file was not found, please contact the website owner.'); ?>');
          break;
        case 'flash': // no flash 9+ :(
          alert('<?php echo __('To enable the embedded uploader, install the latest Adobe Flash plugin.'); ?>')
      }

      console.log(error);
    },

    onComplete: function() {
      console.log('onComplete');

      if (errors.length > 0) {
        // there was at least one error
        document.id('mediator-uploader').destroy();
        document.id('mediator-uploader-file-list').destroy();

        if (hash_keys.length > 0) {
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
    }
  });
});
</script>