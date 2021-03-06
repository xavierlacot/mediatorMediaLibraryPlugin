<?php
$id = sprintf('mediator-media-%s', $mm_media->getId());
$options = $html_options->getRawValue();

if (!isset($options['id']))
{
  $options['id'] = $id;
}

$thumbnail_url = sfConfig::get('app_mediatorMediaLibraryPlugin_media_root', '/media')
  .'/'.mediatorMediaLibraryToolkit::getDirectoryForSize(isset($size) ? $size : 'original')
  .'/'.$mm_media->getmmMediaFolder()->getAbsolutePath()
  .'/'.$mm_media->getThumbnailFilename().'?time='.strtotime($mm_media->getUpdatedAt());
echo image_tag(
  $thumbnail_url,
  $options
);
?>

<?php $pages_count = $mm_media->getMetadata('pages_count'); ?>
<?php if ($pages_count && ($pages_count->getValue() > 1)): ?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#<?php echo $options['id'] ?>").addClass('slider').pageslider({
			  nb_pages:   <?php echo $pages_count->getValue() ?>,
			  images_uri: {
			    <?php $i = 0; ?>
			    <?php while ($i < $pages_count->getValue()): ?>
			      <?php $url = str_replace('-0.png', '-'.$i.'.png', $thumbnail_url); ?>
			      <?php echo sprintf('%d: "%s"', $i, $url); ?>,
			      <?php $i++; ?>
			    <?php endwhile; ?>
			  }
			});
		});
	</script>
<?php endif; ?>