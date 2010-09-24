<?php
class mediatorMediaVideoHandler extends mediatorMediaHandler
{
  /**
   * Deletes all the representations/variations of a media
   *
   * @param  array  sizes of the thumbnails to be deleted
   */
  public function delete(array $sizes)
  {
    if (isset($sizes['original']))
    {
      $this->filesystem->unlink($sizes['original']['directory'].DIRECTORY_SEPARATOR.$this->file);
      $this->filesystem->unlink($sizes['original']['directory'].DIRECTORY_SEPARATOR.$this->file.'.ogg');
      $this->filesystem->unlink($sizes['original']['directory'].DIRECTORY_SEPARATOR.$this->file.'.mp4');
    }

    foreach ($sizes as $format => $size)
    {
      $filename = sprintf(
        '%s.jpg',
        $size['directory'].DIRECTORY_SEPARATOR.$this->file
      );
      $this->filesystem->unlink($filename);
    }
  }

  protected function getAdapter($options = array())
  {
    if (!isset($this->adapter))
    {
      if (!isset($options['class']))
      {
        $adapterClass = 'mediatorMediaVideoFfmpegAdapter';
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
    return array_filter(array(
      'author' => $this->getAdapter()->getAuthor(),
      'duration' => $this->getAdapter()->getDuration(),
      'frame_count' => $this->getAdapter()->getFrameCount(),
      'frame_rate' => $this->getAdapter()->getFrameRate(),
      'height' => $this->getAdapter()->getHeight(),
      'width' => $this->getAdapter()->getWidth(),
    ));
  }

  public function moveTo($absolute_path, $sizes)
  {
    // move the original, along with the ogg and mp4 formats
    if (isset($sizes['original']))
    {
      $this->filesystem->rename(
        $sizes['original']['directory'].DIRECTORY_SEPARATOR.$this->file,
        $sizes['original']['directory'].DIRECTORY_SEPARATOR.$absolute_path.DIRECTORY_SEPARATOR.basename($this->file)
      );
      $this->filesystem->rename(
        $sizes['original']['directory'].DIRECTORY_SEPARATOR.$this->file.'.ogg',
        $sizes['original']['directory'].DIRECTORY_SEPARATOR.$absolute_path.DIRECTORY_SEPARATOR.basename($this->file).'.ogg'
      );
      $this->filesystem->rename(
        $sizes['original']['directory'].DIRECTORY_SEPARATOR.$this->file.'.mp4',
        $sizes['original']['directory'].DIRECTORY_SEPARATOR.$absolute_path.DIRECTORY_SEPARATOR.basename($this->file).'.mp4'
      );
    }

    // move the thumbnails
    foreach ($sizes as $format => $size)
    {
      $this->filesystem->rename(
        $size['directory'].DIRECTORY_SEPARATOR.$this->file.'.jpg',
        $size['directory'].DIRECTORY_SEPARATOR.$absolute_path.DIRECTORY_SEPARATOR.basename($this->file).'.jpg'
      );
    }
  }

  public function process(array $sizes)
  {
    unset($sizes['original']);

    foreach ($sizes as $format => $size)
    {
      if (!isset($size['overwrite'])
          || (true == $size['overwrite'])
          || !$this->filesystem->exists($size['directory'].DIRECTORY_SEPARATOR.$this->file))
      {
        $this->resize($size);
      }
    }

    // generate an extract for the full size also
    $sizes = mediatorMediaLibraryToolkit::getAvailableSizes();
    $this->resize($sizes['original']);

    // generate a mp4 version
    $image = $this->getAdapter()->reEncode('mp4');
    // then save the encoded version
    $filename = sprintf(
      '%s.mp4',
      $sizes['original']['directory'].DIRECTORY_SEPARATOR.$this->file
    );
    $this->filesystem->write($filename, fopen($image, 'r'));
    unlink($image);

    // and a ogg one
    $image = $this->getAdapter()->reEncode('ogg');
    // then save the encoded version
    $filename = sprintf(
      '%s.ogg',
      $sizes['original']['directory'].DIRECTORY_SEPARATOR.$this->file
    );
    $this->filesystem->write($filename, $image);

    return basename($this->file);
  }

  public function resize($options = array())
  {
    $adapter_options = array(
      'scale'     => !isset($options['scale']) || $options['scale'],
      'inflate'   => isset($options['inflate']) && $options['inflate'],
      'crop'      => isset($options['crop']) && $options['crop'],
      'dest_mime' => isset($options['dest_mime']) ? $options['dest_mime'] : 'image/jpeg',
      'quality'   => isset($options['quality']) ? $options['quality'] : 90
    );

    $image = $this->getAdapter()->extractFrame(
      isset($options['width']) ? $options['width'] : null,
      isset($options['height']) ? $options['height'] : null,
      $adapter_options
    );
    $filename = sprintf(
      '%s.jpg',
      $options['directory'].DIRECTORY_SEPARATOR.$this->file
    );
    $this->filesystem->write($filename, $image);
  }

  public function setup($options = array())
  {
    $this->getAdapter($options);
  }
}