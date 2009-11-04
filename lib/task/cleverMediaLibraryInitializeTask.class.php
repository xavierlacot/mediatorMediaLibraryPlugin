<?php
class cleverMediaLibraryInitializeTask extends sfBaseTask
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

    $this->aliases = array('media-initialize');
    $this->namespace = 'media';
    $this->name = 'initialize';
    $this->briefDescription = 'Initializes the media library environement';

    $this->detailedDescription = <<<EOF
The [media:initialize|INFO] task initializes the media library environement:

  [./symfony media:initialize|INFO]

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

    if (count(Doctrine::getTable('CcMediaFolder')->getTree()->fetchRoots()) > 0)
    {
      throw new sfException('The media library has already been initialized!');
    }

    // create the filesystem
    $filesystem = cleverMediaLibraryToolkit::getFilesystem();

    if ($filesystem->exists(''))
    {
      $media_library_root = $filesystem->getRoot();
      throw new sfException(sprintf('The media library folder, %s, already exists. Please delete this folder or change the parameter in the app.yml file !', $media_library_root));
    }
    else
    {
      $filesystem->mkdir('');
    }

    $sizes = cleverMediaLibraryToolkit::getAvailableSizes();

    foreach ($sizes as $size => $params)
    {
      if (!isset($params['directory']))
      {
        throw new sfException(sprintf('Could not create directory for size %s', $size));
      }

      $filesystem->mkdir($params['directory']);
      $filesystem->chmod($params['directory'], 0777);
    }

    $node = new ccMediaFolder();
    $node->setName('Media');
    $node->setRoot(0);
    $node->setFolderPath('');
    $node->setAbsolutePath('');
    $node->save();
    $treeObject = Doctrine::getTable('CcMediaFolder')->getTree();
    $treeObject->createRoot($node);

    $this->log('The media library has been initialized');
   }
}