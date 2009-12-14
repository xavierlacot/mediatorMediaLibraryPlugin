<?php
abstract class mediatorMediaHandler
{
  public function __construct($file, cleverFilesystem $filesystem, $options = array())
  {
    $this->file = $file;
    $this->filesystem = $filesystem;
    $this->options = $options;
    $this->setup();
  }

  public function __destruct()
  {
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

    return basename($this->file);
  }

  public function resize($options = array())
  {
  }

  public function setup($options = array())
  {
  }
}