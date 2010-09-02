<?php
/**
 * Checks that a folder is not a subfolder of another one
 */
class mediatorNotInFolderValidator extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * folder_path:  containing folder path
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('folder_path');

    $this->addMessage('inexistant', 'This folder does not exist.');
    $this->addMessage('contained', 'This folder is a subfolder of %folder%.');
  }

  protected function doClean($value)
  {
    // find destination folder
    $folder = Doctrine::getTable('MmMediaFolder')->findOneById($value);

    if (!$folder)
    {
      // check that it exists
      throw new sfValidatorError($this, 'inexistant', array());
    }

    $path = $folder->getAbsolutePath();
    if (0 === strpos($path, $this->getOption('folder_path')))
    {
      // check that it is not contained in the tested folder
      throw new sfValidatorError($this, 'contained', array('folder' => $this->getOption('folder_path')));
    }

    return $value;
  }
}