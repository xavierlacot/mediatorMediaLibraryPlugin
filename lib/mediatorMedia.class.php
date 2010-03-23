<?php

/**
 * Describes a media file and all its associated variations (thumbnails, etc.)
 */
class mediatorMedia
{
  protected $filename,
            $filesystem,
            $handler,
            $mime_type;

  protected $mime_type_guessers = array(
    'guessFromFileinfo',
    'guessFromMimeContentType',
    'guessFromFileBinary',
    'guessFromExtension',
  );

  protected $media_types = array(
    'application/pdf'        => 'pdf',
    'application/postscript' => '',
    'application/vnd.palm'   => '',
    'application/x-icb'      => '',
    'application/x-mif'      => '',
    'image/dcx'              => '',
    'image/g3fax'            => '',
    'image/gif'              => 'image',
    'image/jng'              => '',
    'image/jpeg'             => 'image',
    'image/pbm'              => '',
    'image/pcd'              => '',
    'image/pict'             => '',
    'image/pjpeg'            => 'image',
    'image/png'              => 'image',
    'image/ras'              => '',
    'image/sgi'              => '',
    'image/svg'              => 'image',
    'image/tga'              => '',
    'image/tiff'             => '',
    'image/vda'              => '',
    'image/vnd.wap.wbmp'     => '',
    'image/vst'              => '',
    'image/x-fits'           => '',
    'image/x-ms-bmp'         => 'image',
    'image/x-otb'            => '',
    'image/x-palm'           => '',
    'image/x-pcx'            => '',
    'image/x-pgm'            => '',
    'image/x-photoshop'      => '',
    'image/x-ppm'            => '',
    'image/x-ptiff'          => '',
    'image/x-viff'           => '',
    'image/x-win-bitmap'     => 'image',
    'image/x-xbitmap'        => 'image',
    'image/x-xv'             => 'image',
    'image/xpm'              => 'image',
    'image/xwd'              => 'image',
    'text/plain'             => 'text',
    'text/x-c'               => 'text',
    'video/mng'              => '',
    'video/mpeg'             => '',
    'video/mpeg2'            => '',
    'application/vnd.oasis.opendocument.text'         => 'office',
    'application/vnd.oasis.opendocument.graphics'     => 'office',
    'application/vnd.oasis.opendocument.presentation' => 'office',
    'application/vnd.oasis.opendocument.spreadsheet'  => 'office',
    'application/vnd.oasis.opendocument.chart'        => 'office',
    'application/vnd.oasis.opendocument.image'        => 'office',
    'application/vnd.sun.xml.writer'                  => 'office',
    'application/vnd.sun.xml.calc'                    => 'office',
    'application/vnd.sun.xml.draw'                    => 'office',
    'application/vnd.sun.xml.impress'                 => 'office',
  );

  protected $file_extensions = array(
    'pdf'    => 'application/pdf',
    'gif'    => 'image/gif',
    'jpg'    => 'image/jpeg',
    'jpeg'   => 'image/jpeg',
    'pjpeg'  => 'image/jpeg',
    'svg'    => 'image/svg',
    'png'    => 'image/png',
    'txt'    => 'text/plain',
    'c'      => 'text/x-c',
    'cpp'    => 'text/x-cpp',
    'php'    => 'text/x-php',
    'doc'    => 'application/msword',
    'xls'    => 'application/vnd.ms-excel',
    'docx'   => 'application/msword',
    'xlsx'   => 'application/vnd.ms-excel',
    'ppt'    => 'application/vnd.ms-powerpoint',
    'odt'    => ' application/vnd.oasis.opendocument.text',
    'odg'    => 'application/vnd.oasis.opendocument.graphics',
    'ods'    => 'application/vnd.oasis.opendocument.spreadsheet',
    'odp'    => 'application/vnd.oasis.opendocument.presentation',
    'odc'    => 'application/vnd.oasis.opendocument.chart',
    'odi'    => 'application/vnd.oasis.opendocument.image',
    'sxw'    => 'application/vnd.sun.xml.writer',
    'sxc'    => 'application/vnd.sun.xml.calc',
    'sxd'    => 'application/vnd.sun.xml.draw',
    'sxi'    => 'application/vnd.sun.xml.impress',
  );

  /**
   * Constructor - loads an file from the disk or a URL
   *
   * @param string filename (with absolute path) of the file to load. If the
   * filename is a http(s) URL, then an attempt to download the file will be
   * made.
   *
   * @return boolean True if the file was properly loaded
   * @throws Exception If the file cannot be loaded, or if its mime type is
   * not supported
   */
  public function __construct($filename, $type = null)
  {
    $this->filename = $filename;
    $this->initialize($type);
  }

  public function __destruct()
  {
    if (isset($this->handler))
    {
      $this->handler->__destruct();
    }

    if (isset($this->filesystem))
    {
      $this->filesystem->__destruct();
    }
  }

  public function cache($force = false)
  {
    $original_path = mediatorMediaLibraryToolkit::getDirectoryForSize('original');
    $filename = $original_path.DIRECTORY_SEPARATOR.$this->filename;
    $this->logger->log(sprintf('{mediatorMedia} caching the media "%s".', $this->filename));
    $return = $this->filesystem->cache($filename, $force);

    if (false === $return)
    {
      $this->logger->log(sprintf('{mediatorMedia} the media could not be cached'));
    }

    return $return;
  }

  protected function configure($options)
  {
    $this->getHandler($options);
  }

  /**
   * Deletes the media file, and all its associated transformations
   */
  public function delete()
  {
    if (is_null($this->getHandler()))
    {
      return null;
    }

    $sizes = mediatorMediaLibraryToolkit::getAvailableSizes();
    return $this->getHandler()->delete($sizes);
  }

  /**
   * Retrieves the associated mm_media object
   *
   * @return mmMedia
   */
  public function getmmMedia()
  {
    return Doctrine::getTable('mmMedia')->findByFilename($this->filename);
  }

  protected function getHandler($options = array())
  {
    if (!isset($this->handler)
        || (isset($options['reload']) && $options['reload']))
    {
      if (isset($this->handler))
      {
        $this->handler->__destruct();
        unset($this->handler);
      }

      if (isset($options['type']) && !is_null($options['type']))
      {
        $handlerClass = 'mediatorMedia'.ucfirst($options['type']).'Handler';

        if (class_exists($handlerClass))
        {
          $this->handler = new $handlerClass($this->filename, $this->filesystem);
        }
        else
        {
          throw new sfException(sprintf('Could not create handler class %s.', $handlerClass));
        }
      }

      if (!isset($this->handler) || is_null($this->handler))
      {
        $this->handler = new mediatorMediaNullHandler($this->filename, $this->filesystem);
      }

      $this->logger->log(sprintf('{mediatorMedia} the media "%s" has a "%s" media handler.', $this->filename, get_class($this->handler)));
    }

    return $this->handler;
  }

  public function getMetadatas()
  {
    if (is_null($this->getHandler()))
    {
      return null;
    }

    $metadatas = $this->getHandler()->getMetadatas();

    if (function_exists('exif_read_data') && ('image/jpeg' === $this->getMimeType()))
    {
      $exif = exif_read_data($this->cache());
      $metadatas = array_merge($exif, $metadatas);
    }

    return $metadatas;
  }

  /**
   * Returns the mime type of a file.
   *
   * This methods call each mime_type_guessers option callables to
   * guess the mime type.
   *
   * @param  string $file      The absolute path of a file
   * @param  string $fallback  The default mime type to return if not guessable
   *
   * @return string The mime type of the file (fallback is returned if not guessable)
   */
  public function getMimeType($fallback = null)
  {
    if (!isset($this->mime_type))
    {
      foreach ($this->mime_type_guessers as $method)
      {
        $type = call_user_func(array($this, $method));

        if (!is_null($type) && ($type !== false) && ($type !== 'application/octet-stream'))
        {
          $this->mime_type = $type;
          return $type;
        }
      }

      return $fallback;
    }
    else
    {
      return $this->mime_type;
    }
  }

  public function getType($fallback = null)
  {
    $mime_type = $this->getMimeType();
    $this->logger->log(sprintf('{mediatorMedia} the media "%s" has the mime-type "%s".', $this->filename, $mime_type));

    if (isset($this->media_types[$mime_type])
        && ('' != $this->media_types[$mime_type]))
    {
      return $this->media_types[$mime_type];
    }
    else
    {
      return $fallback;
    }
  }

  protected function guessFromExtension()
  {
    $original_path = mediatorMediaLibraryToolkit::getDirectoryForSize('original');
    $filename = $original_path.DIRECTORY_SEPARATOR.$this->filename;
    $path_info = pathinfo($filename);
    $extension = $path_info['extension'];

    if (!isset($this->file_extensions[$extension]) || !$this->filesystem->exists($filename))
    {
      return null;
    }
    else
    {
      return $this->file_extensions[$extension];
    }
  }

  /**
   * Guess the file mime type with PECL Fileinfo extension
   *
   * @return string The mime type of the file (null if not guessable)
   */
  protected function guessFromFileinfo()
  {
    $original_path = mediatorMediaLibraryToolkit::getDirectoryForSize('original');
    $filename = $original_path.DIRECTORY_SEPARATOR.$this->filename;

    if (!function_exists('finfo_open') || !$this->filesystem->exists($filename))
    {
      return null;
    }

    if (!$finfo = new finfo(FILEINFO_MIME))
    {
      return null;
    }

    $type = $finfo->file($this->cache());
    $pos = strpos($type, ';');

    if (false !== $pos)
    {
      $type = substr($type, 0, $pos);
    }

    return $type;
  }

  /**
   * Guess the file mime type with mime_content_type function (deprecated)
   *
   * @return string The mime type of the file (null if not guessable)
   */
  protected function guessFromMimeContentType()
  {
    $original_path = mediatorMediaLibraryToolkit::getDirectoryForSize('original');
    $filename = $original_path.DIRECTORY_SEPARATOR.$this->filename;

    if (!function_exists('mime_content_type') || !$this->filesystem->exists($filename))
    {
      return null;
    }

    return mime_content_type($this->cache());
  }

  /**
   * Guess the file mime type with the file binary (only available on *nix)
   *
   * @return string The mime type of the file (null if not guessable)
   */
  protected function guessFromFileBinary()
  {
    ob_start();
    passthru(sprintf('file -bi %s 2>/dev/null', escapeshellarg($this->cache())), $return);

    if ($return > 0)
    {
      return null;
    }

    $type = trim(ob_get_clean());

    if (!preg_match('#^([a-z0-9\-]+/[a-z0-9\-\.]+)#i', $type, $match))
    {
      // it's not a type, but an error message
      return null;
    }

    return $match[1];
  }

  protected function initialize($type)
  {
    try
    {
      $context = sfContext::getInstance();

      if ($context)
      {
        $this->logger = $context->getLogger();
      }
    }
    catch (Exception $e)
    {
      // nothing, context must have launched an exception and we don't care
    }

    if (!isset($this->logger) || !$this->logger)
    {
      $this->logger = new sfNoLogger(new sfEventDispatcher());
    }

    // create the filesystem
    $this->filesystem = mediatorMediaLibraryToolkit::getFilesystem();
    $this->configure(array('type' => $this->getType($type)));
    $this->logger->log(sprintf('{mediatorMedia} the media "%s" has been initialized.', $this->filename));
  }

  public function moveTo($absolute_path)
  {
    $sizes = mediatorMediaLibraryToolkit::getAvailableSizes();
    return $this->getHandler()->moveTo($absolute_path, $sizes);
  }

  /**
   * Creates the thumbnails associated to a mediatorMedia
   *
   * @return string the filename of the thumbnail
   */
  public function process()
  {
    $sizes = mediatorMediaLibraryToolkit::getAvailableSizes();
    unset($sizes['original']);
    return $this->getHandler()->process($sizes);
  }

  public function read()
  {
    return file_get_contents($this->cache());
  }

  public function transform($transformation, $options)
  {
    // transform the media
    $this->getHandler()->$transformation($options);

    // force the local cache to be refreshed
    $this->cache(true);

    // reinit the document handler after the transformation
    $this->getHandler(array('type' => $this->getType(), 'reload' => true));

    // update the document's metadatas
    $this->getmmMedia()->setMetadatas($this->getMetadatas());

    // update the document's variations
    $this->process();
  }


  public function write($source)
  {
    if (preg_match('@^(?:http(s)?://)(.+)@i', $source))
    {
      if (class_exists('sfWebBrowser'))
      {
        $tempFile = tempnam('/tmp', 'mediatorMediaLibraryPlugin');
        $b = new sfWebBrowser();

        try
        {
          $b->get($source);

          if ($b->getResponseCode() != 200)
          {
            throw new sfException(sprintf('%s returned error code %s', $source, $b->getResponseCode()));
          }

          file_put_contents($tempFile, $b->getResponseText());

          if (!filesize($tempFile))
          {
            throw new sfException('downloaded file is empty');
          }
          else
          {
            $source = $tempFile;
          }
        }
        catch (Exception $e)
        {
          throw new sfException("Source file is a URL but it cannot be used because ". $e->getMessage());
        }
      }
      else
      {
        throw new sfException("Source file is a URL but sfWebBrowserPlugin is not installed");
      }
    }
    else
    {
      if (!is_readable($source))
      {
        throw new sfException(sprintf('The file "%s" is not readable.', $source));
      }
    }

    $original_path = mediatorMediaLibraryToolkit::getDirectoryForSize('original');
    $this->filesystem->write(
      $original_path.DIRECTORY_SEPARATOR.$this->filename,
      file_get_contents($source)
    );

    if (isset($tempFile))
    {
      // file has been loaded from http, unlink the temporary cache
      unlink($tempFile);
    }

    $this->cache(true);
    $this->configure(array('type' => $this->getType(), 'reload' => true));
  }
}
