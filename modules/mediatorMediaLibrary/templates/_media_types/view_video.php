<?php if (!isset($size) || 'original' == $size): ?>
  <?php $poster = $mm_media->getUrl(array('extension' => '.jpg')); ?>

  <div class="video-js-box">
    <video id="video-js-<?php echo $mm_media->getId() ?>" class="video-js" width="<?php echo $mm_media->getWidth() ?>" height="<?php echo $mm_media->getHeight() ?>" poster="<?php echo $poster ?>" controls preload>
      <source src="<?php echo $mm_media->getUrl(array('extension' => '.mp4')) ?>" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
      <source src="<?php echo $mm_media->getUrl(array('extension' => '.ogg')) ?>" type='video/ogg; codecs="theora, vorbis"'>
      <object class="vjs-flash-fallback" width="<?php echo $mm_media->getWidth() ?>" height="<?php echo $mm_media->getHeight() ?>" type="application/x-shockwave-flash"
        data="/mediatorMediaLibraryPlugin/swf/flowplayer-3.2.1.swf">
        <param name="movie" value="/mediatorMediaLibraryPlugin/swf/flowplayer-3.2.1.swf" />
        <param name="allowfullscreen" value="true" />
        <param name="flashvars" value='config={"clip":{"url":"<?php echo $mm_media->getUrl(array('absolute' => true, 'extension' => '.mp4')) ?>","autoPlay":false,"autoBuffering":true}}' />

        <img src="<?php echo $poster ?>" width="<?php echo $mm_media->getWidth() ?>" height="<?php echo $mm_media->getHeight() ?>" alt="<?php $mm_media->getBody() ?>"
          title="No video playback capabilities." />
      </object>
    </video>
  </div>
  <script type="text/javascript">
    VideoJS.DOMReady(function(){
      var videoPlayer = VideoJS.setup('video-js-<?php echo $mm_media->getId() ?>');
      videoPlayer.play(); // Starts playing the video for this player.

      $(document).bind('close.facebox', function() {
        videoPlayer.pause()
      });
    });
  </script>
<?php else: ?>
  <?php
  echo image_tag(
    $mm_media->getUrl(array('size' => $size, 'extension' => '.jpg')),
    is_object($html_options) ? $html_options->getRawValue() : $html_options
  );
  ?>
<?php endif; ?>