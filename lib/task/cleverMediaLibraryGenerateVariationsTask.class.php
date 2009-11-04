<?php
class cleverMediaLibraryGenerateVariationsTask extends sfBaseTask
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

    $this->aliases = array('media-generate');
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
    if (0 === count(Doctrine::getTable('CcMediaFolder')->getTree()->fetchRoots()))
    {
      throw new sfException('The media library has not been initialized!');
    }

    // check the filesystem
    $filesystem = cleverMediaLibraryToolkit::getFilesystem();

    if (!$filesystem->exists(''))
    {
      throw new sfException(sprintf('The media library folder, %s, does not exists. Please initialize the library using the media:initialize task !', $media_library_root));
    }

    $sizes = cleverMediaLibraryToolkit::getAvailableSizes();

    // retrieve the ccMediaFolders
    $q = Doctrine_Query::create()
      ->select('f.*')
      ->from('cc_media_folder f');
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

    // retrieve the ccMedia
    $q = Doctrine_Query::create()
      ->select('m.*')
      ->from('cc_media m');
    $medias = $q->execute();
    $total = count($medias);

    foreach ($medias as $key => $media)
    {
      $this->log(sprintf('%s, %s (%s on %s)', memory_get_usage(), $media->getFilename(), $key + 1, $total));
      $media->getCleverMedia()->process();
      $media->__destruct();
      unset($media);
    }

    $this->log('The medias have been processed');
   }
}