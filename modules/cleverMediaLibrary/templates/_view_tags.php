<?php use_helper('Form'); ?>
<?php use_javascript('/sfFormExtraPlugin/js/jquery.autocompleter.js') ?>
<?php use_stylesheet('/sfFormExtraPlugin/css/jquery.autocompleter.css') ?>

<div class="clever_media_library_box">
  <h2><?php echo __('Tags') ?></h2>

  <?php echo form_tag('cleverMediaLibrary/addTag', array('id' => 'clever_media_library_tag_form')) ?>
    <?php echo $tags_form ?>
    <input type="submit" value="<?php echo __('ok') ?>" />
  </form>

  <div id="clever_media_library_tags">
    <?php include_partial('cleverMediaLibrary/tag_list', array('cc_media' => $cc_media)) ?>
  </div>
</div>

<script type="text/javascript">
  //<![CDATA[
  jQuery(document).ready(function($) {
    var deleteItem = function(){
      $(this).parents('dl.sort').remove();
    };

    $("form#clever_media_library_tag_form").submit(function(e) {
      jQuery('#cc_media_tag_name').val(jQuery('#autocomplete_cc_media_tag_name').val());
      jQuery.post('<?php echo sfContext::getInstance()->getController()->genUrl('cleverMediaLibrary/addTag') ?>', $("form#clever_media_library_tag_form").serialize(), function(data) {
        jQuery('#clever_media_library_tags').html(data);
        jQuery('#autocomplete_cc_media_tag_name').val('');
      });
      return false;
    });
  });
  //]]>
</script>