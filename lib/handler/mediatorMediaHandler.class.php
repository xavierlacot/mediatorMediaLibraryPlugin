<?php
abstract class mediatorMediaHandler
{
  protected $adapter;

  public function __construct($file, cleverFilesystem $filesystem, $options = array())
  {
    $this->file = $file;
    $this->filesystem = $filesystem;
    $this->options = $options;
    $this->setup($options);
  }

  public function __destruct()
  {
    if (isset($this->adapter))
    {
      $this->adapter->__destruct();
      unset($this->adapter);
    }

    unset($this);
  }

  abstract function delete(array $options);

  public function getMetadatas()
  {
    return array();
  }

  /**
   * Create the thumbnails of the image in all the dimensions folders
   *
   * @param  array  sizes of the thumbnails to be created
   * @return string name of one thumbnail file
   */
  public function moveTo($absolute_path, $sizes, $new_filename = null)
  {
    if (null == $new_filename)
    {
      $new_filename = basename($this->file);
    }

    foreach ($sizes as $format => $size)
    {
      $this->filesystem->rename(
        $size['directory'].DIRECTORY_SEPARATOR.$this->file,
        $size['directory'].DIRECTORY_SEPARATOR.$absolute_path.DIRECTORY_SEPARATOR.$new_filename
      );
    }
  }

  /**
   * Create the thumbnails of the image in all the dimensions folders
   *
   * @param  array  sizes of the thumbnails to be created
   * @return string name of one thumbnail file
   */
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

    return basename($this->file);
  }

  public function resize($options = array())
  {
  }

  public function setup($options = array())
  {
  }
}