<?php

/**
 * Describes a media file and all its associated variations (thumbnails, etc.)
 */
class mediatorMedia
{
  const META_TYPE_FLASH = 'flash';
  const META_TYPE_IMAGE = 'image';
  const META_TYPE_OFFICE = 'office';
  const META_TYPE_OTHER = 'other';
  const META_TYPE_SOUND = 'sound';
  const META_TYPE_VIDEO = 'video';

  const TYPE_FLASH = 'flash';
  const TYPE_IMAGE = 'image';
  const TYPE_OFFICE = 'office';
  const TYPE_PDF = 'pdf';
  const TYPE_SOUND = 'sound';
  const TYPE_VIDEO = 'video';

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

  public static $media_types = array(
    'application/pdf'                                 => self::TYPE_PDF,
    'application/postscript'                          => '',
    'application/vnd.oasis.opendocument.chart'        => self::TYPE_OFFICE,
    'application/vnd.oasis.opendocument.graphics'     => self::TYPE_OFFICE,
    'application/vnd.oasis.opendocument.image'        => self::TYPE_OFFICE,
    'application/vnd.oasis.opendocument.presentation' => self::TYPE_OFFICE,
    'application/vnd.oasis.opendocument.spreadsheet'  => self::TYPE_OFFICE,
    'application/vnd.oasis.opendocument.text'         => self::TYPE_OFFICE,
    'application/vnd.palm'                            => '',
    'application/vnd.sun.xml.calc'                    => self::TYPE_OFFICE,
    'application/vnd.sun.xml.draw'                    => self::TYPE_OFFICE,
    'application/vnd.sun.xml.impress'                 => self::TYPE_OFFICE,
    'application/vnd.sun.xml.writer'                  => self::TYPE_OFFICE,
    'application/x-icb'                               => '',
    'application/x-mif'                               => '',
    'image/dcx'                                       => '',
    'image/g3fax'                                     => '',
    'image/gif'                                       => self::TYPE_IMAGE,
    'image/jng'                                       => '',
    'image/jpeg'                                      => self::TYPE_IMAGE,
    'image/pbm'                                       => '',
    'image/pcd'                                       => '',
    'image/pict'                                      => '',
    'image/pjpeg'                                     => self::TYPE_IMAGE,
    'image/png'                                       => self::TYPE_IMAGE,
    'image/ras'                                       => '',
    'image/sgi'                                       => '',
    'image/svg'                                       => self::TYPE_IMAGE,
    'image/tga'                                       => '',
    'image/tiff'                                      => self::TYPE_IMAGE,
    'image/vda'                                       => '',
    'image/vnd.wap.wbmp'                              => '',
    'image/vst'                                       => '',
    'image/x-fits'                                    => '',
    'image/x-ms-bmp'                                  => self::TYPE_IMAGE,
    'image/x-otb'                                     => '',
    'image/x-palm'                                    => '',
    'image/x-pcx'                                     => '',
    'image/x-pgm'                                     => '',
    'image/x-photoshop'                               => '',
    'image/x-ppm'                                     => '',
    'image/x-ptiff'                                   => '',
    'image/x-viff'                                    => '',
    'image/x-win-bitmap'                              => self::TYPE_IMAGE,
    'image/x-xbitmap'                                 => self::TYPE_IMAGE,
    'image/x-xv'                                      => self::TYPE_IMAGE,
    'image/xpm'                                       => self::TYPE_IMAGE,
    'image/xwd'                                       => self::TYPE_IMAGE,
    'text/plain'                                      => 'text',
    'text/x-c'                                        => 'text',
    'video/avi'                                       => self::TYPE_VIDEO,
    'video/mng'                                       => '',
    'video/mpeg'                                      => self::TYPE_VIDEO,
    'video/mpeg2'                                     => self::TYPE_VIDEO,
    'video/mp4'                                       => self::TYPE_VIDEO,
    'video/msvideo'                                   => self::TYPE_VIDEO,
    'video/quicktime'                                 => self::TYPE_VIDEO,
    'video/x-msvideo'                                 => self::TYPE_VIDEO,
    'application/x-shockwave-flash'                   => self::TYPE_FLASH,
  );

  public static $file_extensions = array(
    'avi'    => 'video/avi',
    'c'      => 'text/x-c',
    'cpp'    => 'text/x-cpp',
    'doc'    => 'application/msword',
    'docx'   => 'application/msword',
    'gif'    => 'image/gif',
    'jpg'    => 'image/jpeg',
    'jpeg'   => 'image/jpeg',
    'mov'    => 'video/quicktime',
    'mp4'    => 'video/mp4',
    'mpeg'   => 'video/mpeg',
    'mpg'    => 'video/mpeg',
    'odc'    => 'application/vnd.oasis.opendocument.chart',
    'odg'    => 'application/vnd.oasis.opendocument.graphics',
    'odi'    => 'application/vnd.oasis.opendocument.image',
    'odp'    => 'application/vnd.oasis.opendocument.presentation',
    'ods'    => 'application/vnd.oasis.opendocument.spreadsheet',
    'odt'    => 'application/vnd.oasis.opendocument.text',
    'ogg'    => 'video/ogg',
    'pdf'    => 'application/pdf',
    'pjpeg'  => 'image/jpeg',
    'php'    => 'text/x-php',
    'png'    => 'image/png',
    'ppt'    => 'application/vnd.ms-powerpoint',
    'svg'    => 'image/svg',
    'swf'    => 'application/x-shockwave-flash',
    'sxc'    => 'application/vnd.sun.xml.calc',
    'sxd'    => 'application/vnd.sun.xml.draw',
    'sxi'    => 'application/vnd.sun.xml.impress',
    'sxw'    => 'application/vnd.sun.xml.writer',
    'txt'    => 'text/plain',
    'xls'    => 'application/vnd.ms-excel',
    'xlsx'   => 'application/vnd.ms-excel',
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

  protected function getAbsoluteFilename()
  {
    $original_path = mediatorMediaLibraryToolkit::getDirectoryForSize('original');
    return $original_path.DIRECTORY_SEPARATOR.$this->filename;
  }

  public static function getFileExtensions()
  {
    return self::$file_extensions;
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

  public static function getMimeTypeFromFileExtension($extension)
  {
    return isset(self::$file_extensions[$extension]) ? self::$file_extensions[$extension] : null;
  }

  public function getType($fallback = null)
  {
    $mime_type = $this->getMimeType();
    $this->logger->log(sprintf('{mediatorMedia} the media "%s" has the mime-type "%s".', $this->filename, $mime_type));

    if (isset(self::$media_types[$mime_type])
        && ('' != self::$media_types[$mime_type]))
    {
      return self::$media_types[$mime_type];
    }
    else
    {
      return $fallback;
    }
  }

  protected function guessFromExtension()
  {
    $filename = $this->getAbsoluteFilename();
    $path_info = pathinfo($filename);
    $extension = strtolower($path_info['extension']);

    if (!isset(self::$file_extensions[$extension]) || !$this->filesystem->exists($filename))
    {
      return null;
    }
    else
    {
      return self::$file_extensions[$extension];
    }
  }

  /**
   * Guess the file mime type with PECL Fileinfo extension
   *
   * @return string The mime type of the file (null if not guessable)
   */
  protected function guessFromFileinfo()
  {
    $filename = $this->getAbsoluteFilename();

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
    $filename = $this->getAbsoluteFilename();

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
    $filename = $this->getAbsoluteFilename();

    if (!$this->filesystem->exists($filename))
    {
      return null;
    }

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

  public function moveTo($absolute_path, $new_filename = null)
  {
    $sizes = mediatorMediaLibraryToolkit::getAvailableSizes();
    return $this->getHandler()->moveTo($absolute_path, $sizes, $new_filename);
  }

  /**
   * Creates the thumbnails associated to a mediatorMedia
   *
   * @return string the filename of the thumbnail
   */
  public function process()
  {
    $sizes = mediatorMediaLibraryToolkit::getAvailableSizes();
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
      fopen($source, 'r')
    );
    $this->filesystem->chmod(
      $original_path.DIRECTORY_SEPARATOR.$this->filename,
      0777
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
