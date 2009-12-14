<?php
class mediatorMediaTextHandler extends mediatorMediaHandler
{
  /**
   * Deletes all the representations/variations of a media
   *
   * @param  array  options refining the things to be deleted
   */
  public function delete(array $options)
  {
    $original_path = mediatorMediaLibraryToolkit::getDirectoryForSize('original');
    $this->filesystem->unlink($original_path.DIRECTORY_SEPARATOR.$this->file);
  }

  protected function getAdapter($options = array())
  {
    if (!isset($this->adapter))
    {
      if (!isset($options['class']))
      {
        $adapterClass = 'mediatorMediaTextImageMagickAdapter';
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
    $metadatas = array();

    if (isset($this->pages_count))
    {
      $metadatas['pages_count'] = $this->pages_count;
    }

    return $metadatas;
  }

  /**
   * Create the thumbnails of the image in all the dimensions folders
   *
   * @param  array  sizes of the thumbnails to be created
   * @return string name of one thumbnail file
   */
  public function process(array $sizes)
  {
    // generate one image per page
    $pages_count = 0;
    $hasPage = true;
    $adapter = clone $this->getAdapter();
    $directory = mediatorMediaLibraryToolkit::getDirectoryForSize('original');
    $previous_image = '';

    while ($hasPage)
    {
      $image = $adapter->toImage(array('extract' => $pages_count));
      $this->page_filename = sprintf('%s-%s.png', $this->file, $pages_count);
      $hasPage = (false !== $image) && ($image != $previous_image) && ($pages_count < 10);

      if ($hasPage)
      {
        // write the file
        $this->filesystem->write($directory.DIRECTORY_SEPARATOR.$this->page_filename, $image);
        $this->getAdapter()->initialize($this->page_filename, $this->filesystem);

        // then resize it
        foreach ($sizes as $format => $size)
        {
          if (!isset($size['overwrite'])
              || (true == $size['overwrite'])
              || !$this->filesystem->exists($size['directory'].DIRECTORY_SEPARATOR.$this->page_filename))
          {
            $this->resize($size);
          }
        }

        $previous_image = $image;
        $pages_count++;
      }
    }

    $this->pages_count = $pages_count;
    $this->getAdapter()->initialize($this->file, $this->filesystem);
    return basename($this->file).'-0.png';
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
    $this->filesystem->write($options['directory'].DIRECTORY_SEPARATOR.$this->page_filename, $image);
  }
}