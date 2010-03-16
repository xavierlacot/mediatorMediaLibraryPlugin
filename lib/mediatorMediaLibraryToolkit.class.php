<?php
class mediatorMediaLibraryToolkit
{
  public static function getAvailableSizes()
  {
    $defaults = array('original'   => array('directory' => 'original'),
                      'medium'     => array('directory' => 'medium',
                                            'height'    => 300,
                                            'width'     => 300),
                      'small'      => array('directory' => 'small',
                                            'height'    => 100,
                                            'width'     => 100));

    $dir = sfConfig::get('app_mediatorMediaLibraryPlugin_directories', $defaults);
    return array_merge($defaults, $dir);
  }

  public static function getDefaultAdapter($handler_class)
  {
    $adapters = sfConfig::get('app_mediatorMediaLibraryPlugin_default_adapters');

    if (isset($adapters[$handler_class]))
    {
      return $adapters[$handler_class];
    }
    else
    {
      return null;
    }
  }

  public static function getDirectoryForSize($size_name)
  {
    $sizes = self::getAvailableSizes();

    if (isset($sizes[$size_name]['directory']))
    {
      return $sizes[$size_name]['directory'];
    }
    else
    {
      return null;
    }
  }

  public static function getFilesystem()
  {
    $default = array(
      'type'      => 'disk',
      'root'      => sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'media',
      'cache_dir' => '/tmp'
    );
    $fs_name = sfConfig::get('app_mediatorMediaLibraryPlugin_fs', null);

    if (is_string($fs_name))
    {
      $filesystem = cleverFilesystem::getConfiguration($fs_name);
    }

    if (!isset($filesystem) || !$filesystem)
    {
      $filesystem = $default;
    }

    return cleverFilesystem::getInstance($filesystem);
  }
}