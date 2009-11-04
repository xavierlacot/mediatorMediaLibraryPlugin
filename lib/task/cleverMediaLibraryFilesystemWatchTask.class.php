<?php
class cleverMediaLibraryFilesystemWatchTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->aliases = array('media-filesystem-watch');
    $this->namespace = 'media';
    $this->name = 'filesystem-watch';
    $this->briefDescription = 'Watches the filesystem of the media library directory';

    $this->detailedDescription = <<<EOF
The [media:filesystem-watch|INFO] watches the filesystem of the media library directory:

  [./symfony media:filesystem-watch|INFO]

EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // load dtabase configuration before Phing
    $databaseManager = new sfDatabaseManager($this->configuration);
    $context = sfContext::createInstance($this->configuration);

    if (!($nodes = Doctrine::getTable('CcMediaFolder')->getTree()->fetchRoots()))
    {
      throw new sfException('The media library has not been initialized!');
    }

    // create the filesystem
    $this->filesystem = cleverMediaLibraryToolkit::getFilesystem();

    if (!$this->filesystem->exists(''))
    {
      throw new sfException(sprintf('The media library folder, %s, does not exists. Please initialize the library using the media:initialize task!', $media_library_root));
    }

    if (!$this->filesystem->exists(cleverMediaLibraryToolkit::getDirectoryForSize('original')))
    {
      throw new sfException(sprintf('The media library original media folder, %s, does not exists. Please initialize the library using the media:initialize task!', $media_library_root.'/'.cleverMediaLibraryToolkit::getDirectoryForSize('original')));
    }

    $this->results = array();
    $this->checkNode($nodes[0]);
  }

  protected function checkNode($node)
  {
    // get the saved children
    $childrens = $node->getNode()->getChildren();
    $childrens_array = array();

    if ('object' == gettype($childrens))
    {
      foreach ($childrens as $children)
      {
        $childrens_array[$children->getName()] = $children;
      }
    }

    // get the saved files
    $files = $node->getCcMedia();
    $files_array = array();

    foreach ($files as $file)
    {
      $files_array[$file->getFilename()] = $file;
    }

    // get the filesystem content
    $path = cleverMediaLibraryToolkit::getDirectoryForSize('original');

    if ('' != $node->getAbsolutePath())
    {
      $path .= '/'.$node->getAbsolutePath();
    }

    $filesystem_childrens = $this->filesystem->listdir($path);

    foreach ($filesystem_childrens as $filesystem_children)
    {
      $this->log(sprintf('Checking the item "%s"', $filesystem_children));

      if (!isset($childrens_array[$filesystem_children]) && !isset($files_array[$filesystem_children]))
      {
        if ($this->filesystem->isDir($path.'/'.$filesystem_children))
        {
          // directory
          $folder = new ccMediaFolder();
          $fields = array(
            'name'       => $filesystem_children,
            'created_by' => 1,
            'updated_by' => 1,
            'parent'     => $node
          );
          $folder->update($fields);
          $folder->save();
          $this->log(sprintf('The folder "%s" has been created', $folder->getAbsolutePath()));
        }
        else
        {
          // file
          $image = new ccMedia();
          $fields = array(
            'cc_media_folder'     => $node,
            'filename'            => $filesystem_children,
            'updated_by'          => 1,
            'created_by'          => 1,
            'filesystem_existing' => true,
          );
          $image->update($fields);
          $image->save();
        }
      }
      elseif (isset($childrens_array[$filesystem_children]))
      {
        // get the children
        $child = $childrens_array[$filesystem_children];

        // check subfolder's last modification date
        $last_modification_date = $this->filesystem->getLastModificationDate(cleverMediaLibraryToolkit::getDirectoryForSize('original').'/'.$child->getAbsolutePath());
        $node_modification_date = strtotime($child->getUpdatedAt());

        // if updated, check inside this directory
        if (($last_modification_date <= 0) || ($node_modification_date < $last_modification_date))
        {
          $this->log(sprintf('The directory "%s" has been updated, or could not read its modification date', $child->getAbsolutePath()));
          $this->checkNode($child);
        }
      }
      elseif (isset($files_array[$filesystem_children]))
      {
        // get the children
        $file = $files_array[$filesystem_children];

        // check file's last modification date
        $last_modification_date = $this->filesystem->getLastModificationDate(cleverMediaLibraryToolkit::getDirectoryForSize('original').'/'.$file->getAbsoluteFilename());
        $file_modification_date = strtotime($file->getUpdatedAt());

        // if updated, check again and update the file
        // we won't update files changed on filesystems which don't support file last modification date
        if ($file_modification_date < $last_modification_date)
        {
          $this->log(sprintf('The file "%s" has been updated', $file->getAbsolutePath()));

          // check right now
          $file = Doctrine::getTable('CcMedia')->find($file->getId());
          $file_modification_date = strtotime($file->getUpdatedAt());

          // then update
          if ($file_modification_date < $last_modification_date)
          {
            $fields = array(
              'cc_media_folder'     => $node,
              'filename'            => $filesystem_children,
              'updated_by'          => 1,
              'filesystem_existing' => true,
              'filesystem_updating' => true,
            );
            $file->update($fields);
            $file->save();
          }
        }
      }
    }
  }
}