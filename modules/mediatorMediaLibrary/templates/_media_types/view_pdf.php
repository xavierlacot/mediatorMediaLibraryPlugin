<?php
$id = sprintf('mediator-media-%s', $mm_media->getId());
$options = $html_options->getRawValue();

if (!isset($options['id']))
{
  $options['id'] = $id;
}

$thumbnail_url = $mm_media->getUrl(array('extension' => '-0.png', 'size' => isset($size) ? $size : 'original'));
echo image_tag(
  $thumbnail_url,
  $options
);
?>

<?php $pages_count = $mm_media->getMetadata('pages_count'); ?>
<?php if ($pages_count && ($pages_count->getValue() > 1)): ?>
  <?php $sf_response->addJavascript('/mediatorMediaLibraryPlugin/js/jquery.pageslider.js') ?>
	<script type="text/javascript">
	  <?php $limit = sfConfig::get('app_mediatorMediaLibraryPlugin_pdf_max_pages', 10); ?>
		$(document).ready(function(){
			$("#<?php echo $options['id'] ?>").addClass('slider').pageslider({
			  nb_pages:   <?php echo min($pages_count->getValue(), $limit) ?>,
			  prevId:    '<?php echo $options['id'] ?>-prevButton',
			  nextId:    '<?php echo $options['id'] ?>-nextButton',
			  images_uri: {
			    <?php $i = 0; ?>
			    <?php while ($i < $pages_count->getValue() && $i < $limit): ?>
			      <?php $url = str_replace('-0.png', '-'.$i.'.png', $thumbnail_url); ?>
			      <?php echo sprintf('%d: "%s"', $i, $url); ?>,
			      <?php $i++; ?>
			    <?php endwhile; ?>
			  }
			});
		});
	</script>
<?php endif; ?>