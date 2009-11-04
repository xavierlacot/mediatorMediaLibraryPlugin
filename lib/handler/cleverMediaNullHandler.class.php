<?php
class cleverMediaNullHandler extends cleverMediaHandler
{
  /**
   * Deletes all the representations/variations of a media
   *
   * @param  array  options refining the things to be deleted
   */
  public function delete(array $options)
  {
    $original_path = cleverMediaLibraryToolkit::getDirectoryForSize('original');
    $this->filesystem->unlink($original_path.DIRECTORY_SEPARATOR.$this->file);
  }
}