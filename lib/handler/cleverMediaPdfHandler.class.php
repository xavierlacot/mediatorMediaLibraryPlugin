<?php
class cleverMediaPdfHandler extends cleverMediaHandler
{
  protected $adapter;

  /**
   * Deletes all teh representations/variations of a media
   *
   * @param  array  sizes of the thumbnails to be deleted
   */
  public function delete(array $sizes)
  {
    // get the pages number
    $pagesCount = $this->getAdapter()->getPagesCount();
    
    if (isset($sizes['original']))
    {
      $this->filesystem->unlink($sizes['original']['directory'].DIRECTORY_SEPARATOR.$this->file);
      unset($sizes['original']);      
    }

    foreach ($sizes as $format => $size)
    {
      $i = 0;

      while ($i < $pagesCount)
      {
        // delete every pages
        $filename = sprintf(
          '%s-%s.png',
          $size['directory'].DIRECTORY_SEPARATOR.$this->file,
          $i
        );
        $this->filesystem->unlink($filename);
        $i++;
      }
    }
  }

  protected function getAdapter($options = array())
  {
    if (!isset($this->adapter))
    {
      if (!isset($options['class']))
      {
        $adapterClass = 'cleverMediaImageImageMagickAdapter';
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
    return array('pages_count' => $this->getAdapter()->getPagesCount());
  }

  /**
   * Create the thumbnails of the image in all the dimensions folders
   *
   * @param  array  sizes of the thumbnails to be created
   * @return string name of one thumbnail file
   */
  public function process(array $sizes)
  {
    foreach ($sizes as $format => $size)
    {
      if (!isset($size['overwrite']) 
          || (true == $size['overwrite']) 
          || !$this->filesystem->exists($size['directory'].DIRECTORY_SEPARATOR.$this->file))
      {
        $this->resize($size);
      }
    }

    return basename($this->file).'-0.png';
  }

  public function resize($options = array())
  {
    // get the pages number
    $pagesCount = $this->getAdapter()->getPagesCount();

    $adapter_options = array();
    $i = 0;

    while ($i < $pagesCount)
    {
      $adapter_options = array(
        'scale'     => !isset($options['scale']) || $options['scale'],
        'inflate'   => isset($options['inflate']) && $options['inflate'],
        'crop'      => isset($options['crop']) && $options['crop'],
        'dest_mime' => isset($options['dest_mime']) ? $options['dest_mime'] : null,
        'quality'   => isset($options['quality']) ? $options['quality'] : 90,
        'extract'   => $i
      );

      // foreach page, generate the variation for these options
      $image = $this->getAdapter()->resize(
        $options['width'],
        $options['height'],
        $adapter_options
      );
      $filename = sprintf(
        '%s-%s.png',
        $options['directory'].DIRECTORY_SEPARATOR.$this->file,
        $i
      );
      $this->filesystem->write($filename, $image);
      $i++;
    }
  }

  public function setup($options = array())
  {
    $this->getAdapter($options);
  }
}