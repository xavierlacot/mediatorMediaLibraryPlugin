<?php

/**
 * PluginmmMedia
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6716 2009-11-12 19:26:28Z jwage $
 */
abstract class PluginmmMedia extends BasemmMedia
{
  protected $mediatorMedia = null;
  protected
    $_metadatas_unsaved,
    $_metadatas = array();

  public function getMediatorMedia()
  {
    if (!isset($this->mediatorMedia))
    {
      try
      {
        $this->mediatorMedia = new mediatorMedia($this->getAbsoluteFilename());
      }
      catch (Exception $e)
      {
        //throw new sfException($e->getMessage());
      }
    }

    return $this->mediatorMedia;
  }

  public function __destruct()
  {
    if (isset($this->mediatorMedia))
    {
      $this->mediatorMedia->__destruct();
      unset($this->mediatorMedia);
    }

    if (isset($this->handler))
    {
      $this->handler->__destruct();
      unset($this->handler);
    }
  }

  public function __toString()
  {
    return $this->getTitle();
  }

  public function clearMetadatas()
  {
    if ($this->isNew())
    {
      $this->_metadatas_unsaved[] = array();
      return false;
    }

    $q = Doctrine_Query::create()
      ->delete('mmMediaMetadata m')
      ->where('m.mm_media_id = ?', $this->getId());
    $q->execute();
  }

  public function delete(Doctrine_Connection $conn = null)
  {
    $mediator_media = $this->getMediatorMedia();

    if ($mediator_media)
    {
      $mediator_media->delete();
    }

    return parent::delete($conn);
  }

  public function getAbsoluteFilename($mm_media_folder = null)
  {
    if (is_null($mm_media_folder))
    {
      $mm_media_folder = $this->getMmMediaFolder();
    }

    $path = ('' != $mm_media_folder->getAbsolutePath()) ? $mm_media_folder->getAbsolutePath().'/' : '';
    $path .= $this->getFilename();
    return $path;
  }

  /**
   * returns the raw content of the document
   *
   * @return string the content of the file
   */
  public function getContent()
  {
    $mediator_media = $this->getMediatorMedia();

    if ($mediator_media)
    {
      return $mediator_media->getContent();
    }
    else
    {
      return null;
    }
  }

  public function getDisplay($options = array())
  {
    $display_options = array();

    if (isset($options['size']))
    {
      $display_options['size'] =  $options['size'];
      unset($options['size']);
    }
    else
    {
      $display_options['size'] = 'original';
    }

    if (!isset($options['alt']))
    {
      $options['alt'] = $this->getTitle() ? $this->getTitle() : $this->getFilename();
    }

    if (!isset($options['class']))
    {
      $options['class'] = 'mediator-media-preview mediator-media-size-'.$display_options['size'];
    }
    else
    {
      $options['class'] .= ' mediator-media-preview mediator-media-size-'.$display_options['size'];
    }

    $display_options['mm_media'] = $this;
    $display_options['html_options'] = $options;
    $partial = 'mediatorMediaLibrary/media_types/view'.(('' != $this->getType()) ? '_'.$this->getType() : '');
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Partial'));

    try
    {
      $result = get_partial($partial, $display_options);
    }
    catch (Exception $e)
    {
      $result = get_partial('mediatorMediaLibrary/media_types/view', $display_options);
    }

    return $result;
  }

  public function getMetadata($name)
  {
    if ($this->isNew())
    {
      return null;
    }

    if (!isset($this->_metadatas[$name]))
    {
      $metadata = Doctrine_Query::create()
        ->from('mmMediaMetadata m')
        ->where('m.name = ? AND mm_media_id = ?', array($name, $this->id))
        ->fetchOne();

      if ($metadata)
      {
        $this->_metadatas[$name] = $metadata;
      }
    }
    else
    {
      $metadata = $this->_metadatas[$name];
    }

    return $metadata;
  }

  public function getMetadatas()
  {
    $metadatas = Doctrine_Query::create()
      ->from('mmMediaMetadata m')
      ->where('mm_media_id = ?', array($this->id))
      ->execute();

    foreach ($metadatas as $metadata)
    {
      $this->_metadatas[$metadata->getName()] = $metadata;
    }

    return $this->_metadatas;
  }

  public function getUrl($options = array())
  {
    if (!isset($options['size']))
    {
      $options['size'] = 'original';
    }

    $size_directory = mediatorMediaLibraryToolkit::getDirectoryForSize($options['size']);
    $media_path = $this->getAbsoluteFilename();

    if (isset($options['extension']))
    {
      $media_path .= $options['extension'];
    }

    if (sfConfig::get('app_mediatorMediaLibraryPlugin_php_serve_media', false))
    {
      // generate a url to the image path
      $result = sfContext::getInstance()->getController()->genUrl(
        '@mediatorMediaLibrary_medias?size='.$size_directory.'&path='.$media_path
      );
    }
    else
    {
      $result = sfConfig::get('app_mediatorMediaLibraryPlugin_media_root', '/media')
        .'/'.$size_directory
        .'/'.$media_path;
    }

    $result = str_replace(' ', '%20', $result);

    if (isset($options['with_time']) && (true === $options['with_time']))
    {
      $result .= '?time='.strtotime($this->getUpdatedAt());
    }

    $pattern = '~^
      (%s)://                                 # protocol
      (
        ([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
          |                                   #  or
        \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
      )
      (:[0-9]+)?                              # a port (optional)
      (/?|/\S+)                               # a /, nothing or a / with something
    $~ix';

    if (isset($options['absolute'])
      && (true === $options['absolute'])
      && !preg_match($pattern, $result))
    {
      // prepend with the current domain name
      $result = sfContext::getInstance()->getRequest()->getUriPrefix().'/'.$result;
    }

    return $result;
  }

  protected function log($message)
  {
    try
    {
      sfContext::getInstance()->getLogger()->log('{mmMedia}'.$message);
    }
    catch (Exception $e)
    {
      // nothing
    }
  }

  public function hasMetadata($name)
  {
    return (null !== $this->getMetadata($name));
  }

  public function moveTo($array = array())
  {
    if (!isset($array['mm_media_folder']) && !isset($array['filename']))
    {
      throw new sfException('Missing informations in order to move the file.');
    }

    if (isset($array['mm_media_folder']))
    {
      $mm_media_folder = $array['mm_media_folder'];
    }
    else
    {
      $mm_media_folder = $this->getMmMediaFolder();
    }

    $mm_media_folder_path = ('' != $mm_media_folder->getAbsolutePath()) ? $mm_media_folder->getAbsolutePath().DIRECTORY_SEPARATOR : '';

    if (isset($array['filename']))
    {
      $this->getMediatorMedia()->moveTo($mm_media_folder_path, $array['filename']);
      $this->setMmMediaFolder($mm_media_folder);
      $this->setFilename($array['filename']);
      $this->setTitle($array['filename']);
    }
    else
    {
      $this->getMediatorMedia()->moveTo($mm_media_folder_path);
      $this->setMmMediaFolder($mm_media_folder);
    }

    $this->save();
  }

  public function save(Doctrine_Connection $conn = null)
  {
    if ($this->isNew())
    {
      $this->setUuid(uniqid('', true));
    }

    parent::save($conn);

    if (isset($this->_metadatas_unsaved))
    {
      $this->setMetadatas($this->_metadatas_unsaved);
    }
  }

  public function setMetadata($name, $value)
  {
    if ($this->isNew())
    {
      $this->_metadatas_unsaved[$name] = $value;
      return false;
    }

    $metadata = $this->getMetadata($name);

    if (false === $metadata)
    {
      $metadata = new mmMediaMetadata();
      $metadata->setName($name);
      $metadata->setmmMediaId($this->getPrimaryKey());
    }

    $metadata->setValue(''.((string)$value));
    $result = $metadata->save();
    $this->_metadatas[$name] = $metadata;
    return $result;
  }

  public function setMetadatas($metadatas)
  {
    if (is_array($metadatas))
    {
      foreach ($metadatas as $name => $value)
      {
        $this->setMetadata($name, $value);
      }
    }
  }

  public function setSource($source)
  {
    $filename = !is_null($this->getFilename()) ? $this->getFilename() : basename($source);
    $this->update(array(
      'source' => $source,
      'mm_media_folder' => $this->getMmMediaFolder(),
      'filename' => $filename
    ));
  }

  public function update($array = array())
  {
    if (is_null($this->getFilename())
        && ((!isset($array['source'])
            || !isset($array['mm_media_folder'])
            || !isset($array['filename']))
            && (!isset($array['filesystem_existing'])
               || (isset($array['filesystem_existing']) && (true !== $array['filesystem_existing'])))
            )
        )
    {
      throw new sfException('Missing informations in order to update the file.');
    }

    $mm_media_folder = $array['mm_media_folder'];
    unset($array['mm_media_folder']);

    if (!is_null($mm_media_folder) && is_object($mm_media_folder))
    {
      if ($mm_media_folder->isNew())
      {
        $mm_media_folder->save();
      }

      $absolute_path = $mm_media_folder->getAbsolutePath();
      $mm_media_folder_path = ('' != $absolute_path) ? $absolute_path.DIRECTORY_SEPARATOR : '';
      $this->setMmMediaFolderId($mm_media_folder->getPrimaryKey());
    }
    else
    {
      $mm_media_folder_path = $this->getmmMediaFolder()->getAbsolutePath();
    }

    if (isset($array['filename']))
    {
      $filename = mediatorMediaLibraryInflector::cleanFilename($array['filename']);
      unset($array['filename']);
      $this->log(sprintf('The media will be created or updated in "%s"', $mm_media_folder_path.$filename));
      $this->mediatorMedia = new mediatorMedia($mm_media_folder_path.$filename);

      if (isset($array['source']))
      {
        $source = $array['source'];
        unset($array['source']);

        if (!isset($array['filesystem_existing']) || (true !== $array['filesystem_existing']))
        {
          $this->log(sprintf('Writing the media from "%s"', $source));
          $this->mediatorMedia->write($source);
        }
      }

      $this->setMimeType($this->mediatorMedia->getMimeType());
      $type = $this->mediatorMedia->getType();
      $this->setType($type);
      $this->setMd5sum(md5_file($this->mediatorMedia->cache()));
      $this->setFilesize(filesize($this->mediatorMedia->cache()));

      // save the media metadatas
      $metadatas = $this->mediatorMedia->getMetadatas();

      if (isset($metadatas['width']))
      {
        $this->setWidth($metadatas['width']);
        unset($metadatas['width']);
      }

      if (isset($metadatas['height']))
      {
        $this->setHeight($metadatas['height']);
        unset($metadatas['height']);
      }

      $this->setMetadatas($metadatas);

      if (false === sfConfig::get('app_mediatorMediaLibraryPlugin_asynchronous', false))
      {
        $thumbnail_name = $this->mediatorMedia->process();

        if (false !== $thumbnail_name)
        {
          $this->setThumbnailFilename($thumbnail_name);
        }
      }

      $this->setFilename($filename);
      $this->setTitle($filename);
    }

    foreach ($array as $key => $value)
    {
      $fieldNames = Doctrine::getTable('mmMedia')->getColumnNames();

      if (in_array($key, $fieldNames))
      {
        $this->$key = $value;
      }
      else
      {
        $this->setMetadata($key, $value);
      }
    }

    $this->save();
  }
}
