<?php use_javascript('/sfFormExtraPlugin/js/jquery.autocompleter.js') ?>
<?php use_stylesheet('/sfFormExtraPlugin/css/jquery.autocompleter.css') ?>

<div class="mediator_media_library_box">
  <h2><?php echo __('Tags') ?></h2>

  <form action="<?php echo url_for('mediatorMediaLibrary/addTag') ?>" method="post" id="mediator_media_library_tag_form">
    <p>
      <?php echo $tags_form['name'] ?>
      <?php echo $tags_form->renderHiddenFields() ?>
      <input type="submit" value="<?php echo __('ok') ?>" />
    </p>
  </form>

  <div id="mediator_media_library_tags">
    <?php include_partial('mediatorMediaLibrary/tag_list', array('mm_media' => $mm_media)) ?>
  </div>
</div>

<script type="text/javascript">
  //<![CDATA[
  jQuery(document).ready(function($) {
    var deleteItem = function(){
      $(this).parents('dl.sort').remove();
    };

    $("form#mediator_media_library_tag_form").submit(function(e) {
      jQuery('#mm_media_tag_name').val(jQuery('#autocomplete_mm_media_tag_name').val());
      jQuery.post('<?php echo sfContext::getInstance()->getController()->genUrl('mediatorMediaLibrary/addTag') ?>', $("form#mediator_media_library_tag_form").serialize(), function(data) {
        jQuery('#mediator_media_library_tags').html(data);
        jQuery('#autocomplete_mm_media_tag_name').val('');
      });
      return false;
    });
  });
  //]]>
</script>