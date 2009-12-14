<?php

class mediatorMediaImageGDAdapter extends mediatorMediaAdapter
{
  /**
   * List of accepted image types based on MIME
   * descriptions that this adapter supports
   */
  protected $imgTypes = array(
    'image/jpeg',
    'image/pjpeg',
    'image/png',
    'image/gif',
  );

  /**
   * Stores function names for each image type
   */
  protected $imgLoaders = array(
    'image/jpeg'  => 'imagecreatefromjpeg',
    'image/pjpeg' => 'imagecreatefromjpeg',
    'image/png'   => 'imagecreatefrompng',
    'image/gif'   => 'imagecreatefromgif',
  );

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
    $x1 = $options['x1'];
    $y1 = $options['y1'];
    $x2 = $options['x2'];
    $y2 = $options['y2'];

    $dest = imagecreatetruecolor($x2 - $x1, $y2 - $y1);

    if ($x1 == 0 && $y1 == 0 && $x2 == $this->sourceWidth && $y2 = $this->sourceHeight)
    {
      $dest = $this->source;
    }
    else
    {
      imagecopyresampled($dest,
                         $this->source,
                         0,
                         0,
                         $x1,
                         $y1,
                         $x2 - $x1,
                         $y2 - $y1,
                         $x2 - $x1,
                         $y2 - $y1);
    }

    $creator = $this->imgCreators[$this->sourceMime];
    ob_start();
    $creator($dest);
    return ob_get_clean();
  }

  public function getDimensions()
  {
    return array('width' => $this->sourceWidth, 'height' => $this->sourceHeight);
  }

  public function initialize($file, cleverFilesystem $filesystem, $options = array())
  {
    parent::initialize($file, $filesystem, $options);
    $imgData = @getimagesize($this->cache_file);

    if (!$imgData)
    {
      throw new Exception(sprintf('Could not load image %s', $this->cache_file));
    }

    if (!in_array($imgData['mime'], $this->imgTypes))
    {
      throw new Exception(sprintf('Image MIME type %s not supported', $imgData['mime']));
    }
    else
    {
      $loader = $this->imgLoaders[$imgData['mime']];

      if (!function_exists($loader))
      {
        throw new Exception(sprintf('Function %s not available. Please enable the GD extension.', $loader));
      }

      $this->source = $loader($this->cache_file);
      $this->sourceWidth = $imgData[0];
      $this->sourceHeight = $imgData[1];
      $this->sourceMime = $imgData['mime'];
    }
  }

  public function resize($maxWidth = null, $maxHeight = null, $options = array())
  {
    $scale = isset($options['scale']) ? $options['scale'] : true;
    $inflate = isset($options['inflate']) ? $options['inflate'] : false;
    $crop = isset($options['crop']) ? $options['crop'] : false;
    $dest_mime = isset($options['dest_mime']) ? $options['dest_mime'] : null;
    $quality = isset($options['quality']) ? $options['quality'] : 90;

    if (null === $dest_mime)
    {
      $dest_mime = $this->sourceMime;
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
      $dest = $this->source;
    }
    else
    {
      $dest = imagecreatetruecolor($thumbWidth, $thumbHeight);
      imagecopyresampled($dest, $this->source, 0, 0, $sourceLeft, $sourceTop, $thumbWidth, $thumbHeight, $originalWidth, $originalHeight);
    }

    $creator = $this->imgCreators[$dest_mime];
    ob_start();
    $creator($dest);
    return ob_get_clean();
  }
}