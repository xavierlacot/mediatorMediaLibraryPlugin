<?php use_helper('I18N') ?>
<div id="sf_admin_container" class="mediator-media-library">
  <h1><?php echo __('Editing media "%1%"', array('%1%' => $mm_media->getTitle())) ?></h1>
  <?php include_component('mediatorMediaLibrary', 'breadcrumb', array('mm_media' => $mm_media)); ?>

  <div id="mediator_media_library_sidebar">
    <div class="mediator_media_library_box">
      <h2><?php echo __('Croping options') ?></h2>
      <form action="<?php echo url_for('mediatorMediaLibrary/edit?path='.$mm_media->getAbsoluteFilename()) ?>" method="post" id="mediator_media_library_media_edit_form">
        <?php echo $form ?>
        <input type="submit" value="ok" />
      </form>
    </div>

    <div class="mediator_media_library_box">
      <h2><?php echo __('Preview') ?></h2>
      <div style="width:150px;height:150px;overflow:hidden;margin-left:5px;">
        <?php echo $mm_media->getRawValue()->getDisplay(array('size' => 'original', 'id' => 'preview')); ?>
      </div>
    </div>
  </div>

  <div class="sf_admin_form_row" style="clear: none; padding-left: 0;">
    <?php echo $mm_media->getRawValue()->getDisplay(array('size' => 'original', 'id' => 'jcrop_target')); ?>
  </div>

  <script type="text/javascript">
  jQuery(document).ready(function($) {
    var image_width = $(window).width() - 500;
    $('#jcrop_target').width(image_width + 'px');
    var image_height = $('#jcrop_target').height();
    var sides_sum = 300;
    <?php $height = $mm_media->getMetadata('height'); ?>
    <?php $width = $mm_media->getMetadata('width'); ?>
    <?php if (null != $height): ?>
      image_height = <?php echo $height ?>;
    <?php endif; ?>
    <?php if (null != $width): ?>
      image_width = <?php echo $width ?>;
    <?php endif; ?>
    var w_factor = image_width / $('#jcrop_target').width();
    var h_factor = image_height / $('#jcrop_target').height();

    function showPreview(coords) {
      var total = coords.w + coords.h;
      var preview_width = sides_sum * coords.w / total;
      var preview_height = sides_sum * coords.h / total;

      var rx = preview_width / coords.w;
      var ry = preview_height / coords.h;

      $('#preview').css({
        width: Math.round(rx * $('#jcrop_target').width()) + 'px',
        marginLeft: '-' + Math.round(rx * coords.x) + 'px',
        marginTop: '-' + Math.round(ry * coords.y) + 'px'
      });

      $('#mm_media_image_width').val(Math.floor(coords.w * w_factor));
      $('#mm_media_image_height').val(Math.floor(coords.h * h_factor));
      $('#mm_media_image_x1').val(Math.floor(coords.x * w_factor));
      $('#mm_media_image_y1').val(Math.floor(coords.y * h_factor));
      $('#mm_media_image_x2').val(Math.floor(coords.x2 * w_factor));
      $('#mm_media_image_y2').val(Math.floor(coords.y2 * h_factor));

      $('#preview').parent().css({
        width: preview_width + 'px',
        height: preview_height + 'px'
      });
    };

    $('#mm_media_image_width').change(function(e) {
      resizeSelection(e);
    });

    $('#mm_media_image_height').change(function(e) {
      resizeSelection(e);
    });

    // A handler to kill the action
    function nothing(e) {
      e.stopPropagation();
      e.preventDefault();
      return false;
    };

    function resizeSelection(e) {
      var x1 = parseInt($('#mm_media_image_x1').val());
      var y1 = parseInt($('#mm_media_image_y1').val());
      var x2 = x1 + parseInt($('#mm_media_image_width').val());
      var y2 = y1 + parseInt($('#mm_media_image_height').val());

      var ac = [x1 / w_factor, y1 / h_factor, x2 / w_factor, y2 / h_factor];
      crop.animateTo(ac);
      return nothing(e);
    };

    var crop = $.Jcrop('#jcrop_target', {
      onChange: showPreview,
      onSelect: showPreview,
    });
  });
  </script>
  </div>
</div>