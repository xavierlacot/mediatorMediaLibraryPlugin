<?php
/**
 * mediatorMediaImageMagickAdapter is an adapter for images documents, using
 * ImageMagick tools. This adapter is widely based on the sfImageMagickAdapter
 * provided with the sfThumbnail plugin.
 * @see http://www.imagemagick.org
 *
 * @package    mediatorMediaLibraryPlugin
 * @author     Xavier Lacot <xavier@lacot.org>
 */
class mediatorMediaTextImageMagickAdapter extends mediatorMediaImageImageMagickAdapter
{
  public function getPagesCount()
  {

  }

  public function initialize($file, cleverFilesystem $filesystem, $options = array())
  {
    try
    {
      parent::initialize($file, $filesystem, $options);
    }
    catch (Exception $e)
    {
    }

    $this->magickCommands = array();
    $this->magickCommands['convert'] = isset($options['convert']) ? escapeshellcmd($options['convert']) : 'convert';

    exec($this->magickCommands['convert'], $stdout);

    if (strpos($stdout[0], 'ImageMagick') === false)
    {
      throw new Exception(sprintf("ImageMagick convert command not found"));
    }
  }


  public function toImage($options)
  {
    $dest_mime = isset($options['dest_mime']) ? $options['dest_mime'] : null;
    $quality = isset($options['quality']) ? $options['quality'] : 90;
    $extract = isset($options['extract']) ? $options['extract'] : false;

    if (null === $dest_mime)
    {
      $dest_mime = $this->sourceMime;
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
    $targetMime = isset($this->options['targetMime']) ? $this->options['targetMime'] : 'image/png';
    $targetMime = (($mime = array_search($targetMime, $this->mimeMap)) ? $mime.':' : '');
    $targetMime = ('png:' == $targetMime) ? 'png8:' : '';
    $output = $targetMime.$output;
    $cmd = sprintf(
      '%s text:%s%s %s',
      $this->magickCommands['convert'],
      escapeshellarg($this->cache_file),
      $extract,
      escapeshellarg($output)
    );


    ob_start();
    passthru($cmd, $retval);
    $result = ob_get_clean();

    if ($retval)
    {
      return false;
    }

    return $result;
  }
}