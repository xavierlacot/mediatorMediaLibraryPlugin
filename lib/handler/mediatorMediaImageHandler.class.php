<?php
class mediatorMediaImageHandler extends mediatorMediaHandler
{
  protected $adapter;

  public function __destruct()
  {
    if (isset($this->adapter))
    {
      $this->adapter->__destruct();
      unset($this->adapter);
    }

    parent::__destruct();
  }

  public function crop($options)
  {
    $image = $this->getAdapter()->crop($options);
    $original_path = mediatorMediaLibraryToolkit::getDirectoryForSize('original');

    // write the croped image in the original folder
    $this->filesystem->write($original_path.DIRECTORY_SEPARATOR.$this->file, $image);

    // reinitialize the adapter
    $this->getAdapter()->reinitialize($this->file, $this->filesystem, $options);
  }

  /**
   * Deletes all the representations/variations of a media
   *
   * @param  array  sizes of the thumbnails to be deleted
   */
  public function delete(array $options)
  {
    foreach ($options as $format => $size)
    {
      $this->filesystem->unlink($size['directory'].DIRECTORY_SEPARATOR.$this->file);
    }
  }

  protected function getAdapter($options = array())
  {
    if (!isset($this->adapter))
    {
      if (!isset($options['class']))
      {
        // get default adapter
        $adapterClass = mediatorMediaLibraryToolkit::getDefaultAdapter(get_class($this));

        if (!$adapterClass)
        {
          if (extension_loaded('gd'))
          {
            $adapterClass = 'mediatorMediaImageGDAdapter';
          }
          else
          {
            $adapterClass = 'mediatorMediaImageImageMagickAdapter';
          }
        }
      }
      else
      {
        $adapterClass = $options['class'];
        unset($options['class']);
      }

      $this->adapter = new $adapterClass($this->file, $this->filesystem, $options);
    }

    return $this->adapter;
  }

  public function getMetadatas()
  {
    // get dimensions from image adapter
    return $this->getAdapter()->getDimensions();
  }

  public function resize($options = array())
  {
    $adapter_options = array(
      'scale'     => !isset($options['scale']) || $options['scale'],
      'inflate'   => isset($options['inflate']) && $options['inflate'],
      'crop'      => isset($options['crop']) && $options['crop'],
      'dest_mime' => isset($options['dest_mime']) ? $options['dest_mime'] : null,
      'quality'   => isset($options['quality']) ? $options['quality'] : 90
    );

    $image = $this->getAdapter()->resize(
      $options['width'],
      $options['height'],
      $adapter_options
    );
    $this->filesystem->write($options['directory'].DIRECTORY_SEPARATOR.$this->file, $image);
  }

  public function setup($options = array())
  {
    $this->getAdapter($options);
  }
}