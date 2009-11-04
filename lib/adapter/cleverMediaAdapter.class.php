<?php

abstract class cleverMediaAdapter
{
  protected $cache_dir;

  public function __construct($file, cleverFilesystem $filesystem, $options = array())
  {
    $this->initialize($file, $filesystem, $options);
  }
  
  public function __destruct()
  {
    unset($this->file);
    unset($this->source);
    unset($this->cache_file);
    unset($this->filesystem);
  }
  
  public function getPagesCount()
  {
    return 1;
  }

  public function initialize($file, cleverFilesystem $filesystem, $options = array())
  {    
    $this->file = $file;
    $this->filesystem = $filesystem;
    $original_path = cleverMediaLibraryToolkit::getDirectoryForSize('original');
    $filename = $original_path.DIRECTORY_SEPARATOR.$this->file;
    $this->cache_file = $this->filesystem->cache($filename);
    $this->options = $options;
  }

  public function reinitialize($file, cleverFilesystem $filesystem, $options = array())
  {
    $this->initialize($file, $filesystem, $options);
  }
}