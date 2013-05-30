<?php
class mediatorMediaLibraryGenerateVariationsTask extends sfBaseTask
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

    $this->namespace = 'media';
    $this->name = 'generate';
    $this->briefDescription = 'Generates the variations of all the media included in the media library';

    $this->detailedDescription = <<<EOF
The [media:generate|INFO] generates the variations of all the media included in the media library:

  [./symfony media:generate|INFO]

EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // load database configuration before Phing
    $databaseManager = new sfDatabaseManager($this->configuration);
    $context = sfContext::createInstance($this->configuration);

    // check the root existence
    if (0 === count(Doctrine::getTable('MmMediaFolder')->getTree()->fetchRoots()))
    {
      throw new sfException('The media library has not been initialized!');
    }

    // check the filesystem
    $filesystem = mediatorMediaLibraryToolkit::getFilesystem();

    if (!$filesystem->exists(''))
    {
      throw new sfException(sprintf('The media library folder, %s, does not exists. Please initialize the library using the media:initialize task !', $media_library_root));
    }

    $sizes = mediatorMediaLibraryToolkit::getAvailableSizes();

    // retrieve the mmMediaFolders
    $q = Doctrine_Query::create()
      ->select('f.*')
      ->from('mmMediaFolder f');
    $media_folders = $q->execute();

    foreach ($sizes as $size => $params)
    {
      if (!isset($params['directory']))
      {
        throw new sfException(sprintf('Could not create directory for size %s', $size));
      }

      if (!$filesystem->exists($params['directory']))
      {
        $filesystem->mkdir($params['directory']);

        // also create all the subfolders
        foreach ($media_folders as $media_folder)
        {
          $path = $media_folder->getAbsolutePath();

          if (!$filesystem->exists($params['directory'].DIRECTORY_SEPARATOR.$path))
          {
            $filesystem->mkdir($params['directory'].DIRECTORY_SEPARATOR.$path);
          }
        }
      }
    }

    // retrieve the mmMedia
    $q = Doctrine_Query::create()
      ->select('m.*')
      ->from('mmMedia m');
    $medias = $q->execute();
    $total = count($medias);

    foreach ($medias as $key => $media)
    {
      $this->log(sprintf('%s, %s (%s on %s)', memory_get_usage(), $media->getFilename(), $key + 1, $total));
      $media->getMediatorMedia()->process();
      $media->__destruct();
      unset($media);
    }

    $this->log('The medias have been processed');
   }
}
