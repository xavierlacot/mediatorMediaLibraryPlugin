<?php if (!isset($size) || 'original' == $size): ?>
  <?php $poster = $mm_media->getUrl(array('extension' => '.jpg')); ?>

  <?php $sf_response->addJavascript('/mediatorMediaLibraryPlugin/js/video.js') ?>
  <?php $sf_response->addStylesheet('/mediatorMediaLibraryPlugin/css/video-js.css') ?>

  <video class="video-js" width="<?php echo $mm_media->getWidth() ?>" height="<?php echo $mm_media->getHeight() ?>" poster="<?php echo $poster ?>" controls preload>
    <source src="<?php echo $mm_media->getUrl(array('extension' => '.mp4')) ?>" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
    <source src="<?php echo $mm_media->getUrl(array('extension' => '.ogg')) ?>" type='video/ogg; codecs="theora, vorbis"'>
    <object class="vjs-flash-fallback" width="<?php echo $mm_media->getWidth() ?>" height="<?php echo $mm_media->getHeight() ?>" type="application/x-shockwave-flash"
      data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">
      <param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />
      <param name="allowfullscreen" value="true" />
      <param name="flashvars" value='config={"clip":{"url":"<?php echo $mm_media->getUrl(array('extension' => '.mp4')) ?>","autoPlay":false,"autoBuffering":true}}' />

      <img src="<?php echo $poster ?>" width="<?php echo $mm_media->getWidth() ?>" height="<?php echo $mm_media->getHeight() ?>" alt="<?php $mm_media->getBody() ?>"
        title="No video playback capabilities." />
    </object>
  </video>
<?php else: ?>
  <?php
  echo image_tag(
    $mm_media->getUrl(array('size' => $size, 'extension' => '.jpg')),
    is_object($html_options) ? $html_options->getRawValue() : $html_options
  );
  ?>
<?php endif; ?>