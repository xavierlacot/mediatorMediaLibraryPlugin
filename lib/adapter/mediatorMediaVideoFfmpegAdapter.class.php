<?php
/**
 * mediatorMediaVideoFfmpegAdapter is an adapter for video documents,
 * using ffmpeg and ffmpeg-php.
 *
 * @package    mediatorMediaLibraryPlugin
 * @author     Xavier Lacot <xavier@lacot.org>
 */
class mediatorMediaVideoFfmpegAdapter extends mediatorMediaAdapter
{
  protected
    $duration,
    $movie;

  /**
   * Stores function names for each image type
   */
  protected $imgCreators = array(
    'image/jpeg'  => 'imagejpeg',
    'image/pjpeg' => 'imagejpeg',
    'image/png'   => 'imagepng',
    'image/gif'   => 'imagegif',
  );

  public function crop($options)
  {
    throw new sfException('Cannot crop a video');
  }

  public function reEncode($format)
  {
    $ffmpeg_options = array(
      'mp4' => '-acodec aac -strict experimental',
      'ogg' => '-acodec libvorbis -ar 22050 -ab 48k',
    );

    if (!isset($ffmpeg_options[$format]))
    {
      throw new sfException('The video adapter can not reencode in this format!');
    }

    // encode localy
    $tempFile = tempnam('/tmp', 'mediatorMediaLibraryPlugin').'.'.$format;

    $command = sprintf(
      '%s -i %s -s 384x288 -b 300k %s %s',
      $this->commands['ffmpeg'],
      escapeshellarg($this->cache_file),
      $ffmpeg_options[$format],
      $tempFile
    );

    ob_start();
    passthru($command);
    ob_get_clean();
    return fopen($tempFile, 'r');
  }

  public function extractFrame($maxWidth = null, $maxHeight = null, $options = array())
  {
    $frame_number = 1 + floor($this->getMovie()->getFrameCount() / 2);
    $frame = $this->getMovie()->getFrame($frame_number);

    if (!$frame)
    {
      throw new sfException(sprintf('Could not extract frame number %s.', $frame_number));
    }

    $scale = isset($options['scale']) ? $options['scale'] : true;
    $inflate = isset($options['inflate']) ? $options['inflate'] : false;
    $crop = isset($options['crop']) ? $options['crop'] : false;
    $dest_mime = isset($options['dest_mime']) ? $options['dest_mime'] : null;
    $quality = isset($options['quality']) ? $options['quality'] : 90;

    if (null === $dest_mime)
    {
      $dest_mime = 'image/jpeg';
    }

    if (null === $maxWidth)
    {
      $maxWidth = $this->sourceWidth;
    }

    if (null === $maxHeight)
    {
      $maxHeight = $this->sourceHeight;
    }

    if ($maxWidth > 0)
    {
      $ratioWidth = $maxWidth / $this->sourceWidth;
    }

    if ($maxHeight > 0)
    {
      $ratioHeight = $maxHeight / $this->sourceHeight;
    }

    if ($scale)
    {
      if ($maxWidth && $maxHeight)
      {
        $ratio = ($ratioWidth < $ratioHeight) ? $ratioWidth : $ratioHeight;
      }

      if ($maxWidth xor $maxHeight)
      {
        $ratio = (isset($ratioWidth)) ? $ratioWidth : $ratioHeight;
      }

      if ((!$maxWidth && !$maxHeight) || (!$inflate && $ratio > 1))
      {
        $ratio = 1;
      }

      $thumbWidth = floor($ratio * $this->sourceWidth);
      $thumbHeight = ceil($ratio * $this->sourceHeight);
    }
    else
    {
      if (!isset($ratioWidth) || (!$inflate && $ratioWidth > 1))
      {
        $ratioWidth = 1;
      }

      if (!isset($ratioHeight) || (!$inflate && $ratioHeight > 1))
      {
        $ratioHeight = 1;
      }

      $thumbWidth = floor($ratioWidth * $this->sourceWidth);
      $thumbHeight = ceil($ratioHeight * $this->sourceHeight);
    }

    if ($crop)
    {
      $thumbWidth = $maxWidth;
      $thumbHeight = $maxHeight;

      // compute top, left, width, height
      $originalSize = min($this->sourceWidth, $this->sourceHeight);
      $sourceTop = ($this->sourceHeight - $originalSize) / 2;
      $sourceLeft = ($this->sourceWidth - $originalSize) / 2;
      $originalWidth = $originalSize;
      $originalHeight = $originalSize;
    }
    else
    {
      $sourceTop = 0;
      $sourceLeft = 0;
      $originalWidth = $this->sourceWidth;
      $originalHeight = $this->sourceHeight;
    }

    if ($this->sourceWidth == $maxWidth && $this->sourceHeight == $maxHeight)
    {
      $dest = $frame->toGDImage();
    }
    else
    {
      $dest = imagecreatetruecolor($thumbWidth, $thumbHeight);
      imagecopyresampled(
        $dest,
        $frame->toGDImage(),
        0,
        0,
        $sourceLeft,
        $sourceTop,
        $thumbWidth,
        $thumbHeight,
        $originalWidth,
        $originalHeight
      );
    }

    $creator = $this->imgCreators[$dest_mime];
    ob_start();
    $creator($dest);
    return ob_get_clean();
  }

  public function getAuthor()
  {
    return $this->getMovie()->getAuthor();
  }

  public function getDuration()
  {
    return $this->getMovie()->getDuration();
  }

  public function getFrameCount()
  {
    return $this->getMovie()->getFrameCount();
  }

  public function getFrameRate()
  {
    return $this->getMovie()->getFrameRate();
  }

  protected function getMovie()
  {
    if (!$this->movie)
    {
      $this->movie = new ffmpeg_movie($this->cache_file);
    }

    return $this->movie;
  }

  public function getHeight()
  {
    return $this->sourceHeight;
  }

  public function getWidth()
  {
    return $this->sourceWidth;
  }

  public function initialize($file, cleverFilesystem $filesystem, $options = array())
  {
    parent::initialize($file, $filesystem, $options);

    if (!$this->getMovie()->hasVideo())
    {
      throw new sfException('This movie does not have a video stream');
    }

    $this->commands['ffmpeg'] = isset($options['ffmpeg']) ? escapeshellcmd($options['ffmpeg']) : 'ffmpeg';
    exec($this->commands['ffmpeg'].' -version', $stdout);

    if (strpos($stdout[0], 'FFmpeg') === false)
    {
      throw new sfException(sprintf("FFmpeg command not found"));
    }

    $this->sourceHeight = $this->getMovie()->getFrameHeight();
    $this->sourceWidth = $this->getMovie()->getFrameWidth();
  }
}