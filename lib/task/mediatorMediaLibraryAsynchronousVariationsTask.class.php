<?php
class mediatorMediaLibraryAsynchronousVariationsTask extends sfBaseTask
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
    $this->name = 'asynchronous-variations';
    $this->briefDescription = 'Generates the variations of all the media newly uploaded in the media library';

    $this->detailedDescription = <<<EOF
The [media:asynchronous-variations|INFO] generates the variations of all the media newly uploaded in the media library:

  [./symfony media:asynchronous-variations|INFO]

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

    // retrieve the mmMedia
    $q = Doctrine_Query::create()
      ->select('m.*')
      ->from('mmMedia m')
      ->where('m.thumbnail_filename IS NULL');
    $medias = $q->execute();

    // mark them as pending
    $q = Doctrine_Query::create()
      ->update('mmMedia m')
      ->set('m.thumbnail_filename', '""')
      ->where('m.thumbnail_filename IS NULL')
      ->execute();

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