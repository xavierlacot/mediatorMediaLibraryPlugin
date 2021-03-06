<?php
/**
 * mediatorMediaImageMagickAdapter is an adapter for images documents, using
 * ImageMagick tools. This adapter is widely based on the sfImageMagickAdapter
 * provided with the sfThumbnail plugin.
 * @see http://www.imagemagick.org
 *
 * @package    mediatorMediaLibraryPlugin
 * @author     Xavier Lacot <xavier@lacot.org>
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Benjamin Meynell <bmeynell@colorado.edu>
 */
class mediatorMediaImageImageMagickAdapter extends mediatorMediaAdapter
{
  protected $magickCommands,
            $pagesCount,
            $sourceHeight,
            $sourceMime,
            $sourceWidth;

  /**
   * Imagemagick-specific Type to Mime type map
   */
  protected $mimeMap = array(
    'bmp'   => 'image/bmp',
    'bmp2'  => 'image/bmp',
    'bmp3'  => 'image/bmp',
    'cur'   => 'image/x-win-bitmap',
    'dcx'   => 'image/dcx',
    'epdf'  => 'application/pdf',
    'epi'   => 'application/postscript',
    'eps'   => 'application/postscript',
    'eps2'  => 'application/postscript',
    'eps3'  => 'application/postscript',
    'epsf'  => 'application/postscript',
    'epsi'  => 'application/postscript',
    'ept'   => 'application/postscript',
    'ept2'  => 'application/postscript',
    'ept3'  => 'application/postscript',
    'fax'   => 'image/g3fax',
    'fits'  => 'image/x-fits',
    'g3'    => 'image/g3fax',
    'gif'   => 'image/gif',
    'gif87' => 'image/gif',
    'icb'   => 'application/x-icb',
    'ico'   => 'image/x-win-bitmap',
    'icon'  => 'image/x-win-bitmap',
    'jng'   => 'image/jng',
    'jpeg'  => 'image/jpeg',
    'jpg'   => 'image/jpeg',
    'm2v'   => 'video/mpeg2',
    'miff'  => 'application/x-mif',
    'mng'   => 'video/mng',
    'mpeg'  => 'video/mpeg',
    'mpg'   => 'video/mpeg',
    'otb'   => 'image/x-otb',
    'p7'    => 'image/x-xv',
    'palm'  => 'image/x-palm',
    'pbm'   => 'image/pbm',
    'pcd'   => 'image/pcd',
    'pcds'  => 'image/pcd',
    'pcl'   => 'application/pcl',
    'pct'   => 'image/pict',
    'pcx'   => 'image/x-pcx',
    'pdb'   => 'application/vnd.palm',
    'pdf'   => 'application/pdf',
    'pgm'   => 'image/x-pgm',
    'picon' => 'image/xpm',
    'pict'  => 'image/pict',
    'pjpeg' => 'image/pjpeg',
    'png'   => 'image/png',
    'png24' => 'image/png',
    'png32' => 'image/png',
  );

  public function autoRotate()
  {
    $quality = isset($options['quality']) ? $options['quality'] : 90;
    $command = '-auto-orient';

    if ($quality && $this->sourceMime == 'image/jpeg')
    {
      $command .= ' -quality '.$quality.'% ';
    }

    $output = '-';
    $output = (($mime = array_search($this->sourceMime, $this->mimeMap)) ? $mime.':' : '').$output;
    $cmd = $this->magickCommands['convert'].' '.$command.' '.escapeshellarg($this->cache_file).' '.escapeshellarg($output);

    ob_start();
    passthru($cmd);
    return ob_get_clean();
  }

  public function crop($options)
  {
    $x1 = $options['x1'];
    $y1 = $options['y1'];
    $x2 = $options['x2'];
    $y2 = $options['y2'];
    $command = sprintf(
      ' -crop %sx%s+%s+%s',
      abs($x2 - $x1),
      abs($y2 - $y1),
      $x1,
      $y1
    );
    $quality = isset($options['quality']) ? $options['quality'] : 90;
    $dest_mime = isset($options['dest_mime']) ? $options['dest_mime'] : null;

    if (null === $dest_mime)
    {
      $dest_mime = $this->sourceMime;
    }

    if ($quality && $dest_mime == 'image/jpeg')
    {
      $command .= ' -quality '.$quality.'% ';
    }

    $output = '-';
    $output = (($mime = array_search($dest_mime, $this->mimeMap)) ? $mime.':' : '').$output;
    $cmd = $this->magickCommands['convert'].' '.$command.' '.escapeshellarg($this->cache_file).' '.escapeshellarg($output);

    ob_start();
    passthru($cmd);
    return ob_get_clean();
  }

  public function getDimensions()
  {
    return array('width' => $this->sourceWidth, 'height' => $this->sourceHeight);
  }

  public function getPagesCount()
  {
    return $this->pagesCount;
  }

  public function initialize($file, cleverFilesystem $filesystem, $options = array())
  {
    parent::initialize($file, $filesystem, $options);
    $this->magickCommands = array();
    $this->magickCommands['convert'] = isset($options['convert']) ? escapeshellcmd($options['convert']) : 'convert';
    $this->magickCommands['identify'] = isset($options['identify']) ? escapeshellcmd($options['identify']) : 'identify';

    exec($this->magickCommands['convert'], $stdout);

    if (strpos($stdout[0], 'ImageMagick') === false)
    {
      throw new Exception(sprintf("ImageMagick convert command not found"));
    }

    exec($this->magickCommands['identify'], $stdout);

    if (strpos($stdout[0], 'ImageMagick') === false)
    {
      throw new Exception(sprintf("ImageMagick identify command not found"));
    }

    ob_start();
    passthru($this->magickCommands['identify'].' '.escapeshellarg($this->cache_file), $retval);
    $stdout = ob_get_clean();
    $stdout = explode('
', trim($stdout));
    $this->pagesCount = count($stdout);

    if ($retval)
    {
      throw new sfException(sprintf('Image %s could not be identified.', $file));
    }
    else
    {
      // get image data via identify
      list($img, $type, $dimen) = explode(' ', substr($stdout[0], strlen($this->cache_file)));
      list($width, $height) = explode('x', $dimen);
      $this->sourceMime = $this->mimeMap[strtolower($type)];
      $this->sourceWidth = $width;
      $this->sourceHeight = $height;
    }
  }


  public function resize($maxWidth = null, $maxHeight = null, $options = array())
  {
    $scale = isset($options['scale']) ? $options['scale'] : true;
    $inflate = isset($options['inflate']) ? $options['inflate'] : false;
    $crop = isset($options['crop']) ? $options['crop'] : false;
    $dest_mime = isset($options['dest_mime']) ? $options['dest_mime'] : null;
    $quality = isset($options['quality']) ? $options['quality'] : 90;
    $extract = isset($options['extract']) ? $options['extract'] : false;

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

    $command = '';

    if ($crop)
    {
      $thumbWidth = $maxWidth;
      $thumbHeight = $maxHeight;
      $command .= ' -resize '.$thumbWidth.'x'.$thumbHeight.'^ -gravity center -crop '.$thumbWidth.'x'.$thumbHeight.'+0x0';
    }
    else
    {
      $sourceTop = 0;
      $sourceLeft = 0;
      $originalWidth = $this->sourceWidth;
      $originalHeight = $this->sourceHeight;
      $command .= ' -thumbnail '.$thumbWidth.'x'.$thumbHeight;

      // absolute sizing
      if (!$scale)
      {
        $command .= '!';
      }
    }

    if ($quality && $dest_mime == 'image/jpeg')
    {
      $command .= ' -quality '.$quality.'% ';
    }

    // extract images such as pages from a pdf doc
    if (is_int($extract) && ($extract >= 0))
    {
      $extract = '['.escapeshellarg($extract).'] ';
    }

    $output = '-';
    $output = (($mime = array_search($dest_mime, $this->mimeMap)) ? $mime.':' : '').$output;
    $cmd = $this->magickCommands['convert'].' '.$command.' '.escapeshellarg($this->cache_file).$extract.' '.escapeshellarg($output);

    ob_start();
    passthru($cmd);
    return ob_get_clean();
  }

  public function rotate($angle, $options = array())
  {
    $dest_mime = isset($options['dest_mime']) ? $options['dest_mime'] : null;
    $quality = isset($options['quality']) ? $options['quality'] : 90;

    if (null === $dest_mime)
    {
      $dest_mime = $this->sourceMime;
    }

    $command = '-rotate '.$angle;

    if ($quality && $dest_mime == 'image/jpeg')
    {
      $command .= ' -quality '.$quality.'% ';
    }

    $output = '-';
    $output = (($mime = array_search($dest_mime, $this->mimeMap)) ? $mime.':' : '').$output;
    $cmd = $this->magickCommands['convert'].' '.$command.' '.escapeshellarg($this->cache_file).' '.escapeshellarg($output);
    ob_start();
    passthru($cmd);
    return ob_get_clean();
  }
}