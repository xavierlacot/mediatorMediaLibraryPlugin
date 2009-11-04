<?php use_helper('Tags'); ?>
<?php $tags = $cc_media->getTags(); ?>
<?php if (count($tags) > 0): ?>
  <ul class="tag_list">
    <?php foreach ($tags as $tag) : ?>
      <li>
        <a href="#" class="delete_tag"><?php echo image_tag('/cleverMediaLibraryPlugin/images/icons/delete-small.png', array('alt' => __('delete'))); ?></a>
        <a rel="tag"><?php echo $tag ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <p><?php echo __('No tag is attached to this media.') ?></p>
<?php endif; ?>

<script type="text/javascript">
  //<![CDATA[
  jQuery(document).ready(function($) {
    var deleteItem = function(){
      $(this).parents('dl.sort').remove();
    };

    $("a.delete_tag").click(function(e) {
      var value = jQuery(this).siblings('a[rel=tag]').html();

      if (!confirm('Do you really want to remove the tag "' + value + '" ?')) {
        return false;
      }

      jQuery.post(
        '<?php echo sfContext::getInstance()->getController()->genUrl('cleverMediaLibrary/deleteTag') ?>',
        {'tagging[]':['<?php echo $cc_media->getRawValue()->getId() ?>', value]},
        function(data) {
          jQuery('#clever_media_library_tags').html(data);
          return false;
        }
      );

      return false;
    });
  });
  //]]>
</script>