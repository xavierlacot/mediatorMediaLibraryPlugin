<?php

/**
 * PluginmmMediaFolder
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6716 2009-11-12 19:26:28Z jwage $
 */
abstract class PluginmmMediaFolder extends BasemmMediaFolder
{
  public function __toString()
  {
    return $this->getName();
  }

  public function __toStringWithDepth()
  {
    return str_repeat("&nbsp;&nbsp;&nbsp;", $this->getLevel()).$this->__toString();
  }

  public function __call($method, $arguments)
  {
    $methods = array(
      'getAncestors',
      'getChildren',
      'getDescendants',
      'getLevel',
      'getNode',
      'getParent',
      'insertAsLastChildOf',
      'isLeaf',
      'isRoot',
      'moveAsLastChildOf'
    );

    if (in_array($method, $methods) &&
      !sfConfig::get('app_mediatorMediaLibraryPlugin_use_nested_set', false))
    {
      $method = '__adjancy_list_'.$method;
      return call_user_func_array(
        array($this, $method),
        $arguments
      );
    }
    else
    {
      return parent::__call($method, $arguments);
    }
  }

  public function getNode()
  {
    if (!sfConfig::get('app_mediatorMediaLibraryPlugin_use_nested_set', false))
    {
      return $this;
    }
  }

  public function __adjancy_list_getAncestors()
  {
    if ($this->isRoot())
    {
      return array();
    }
    else
    {
      $parent = $this->getParent();
      $ancestors = $parent->getAncestors();
      $ancestors[] = $parent;
      return $ancestors;
    }
  }

  public function __adjancy_list_getChildren()
  {
    $q = Doctrine_Query::create()
      ->from('mmMediaFolder m')
      ->where('m.parent_id = ?', $this->getId())
      ->andWhere('m.root = ?', $this->getRoot());
    return $q->execute();
  }

  public function __adjancy_list_getDescendants()
  {
    if ($this->isLeaf())
    {
      return array();
    }
    else
    {
      $children = $this->getChildren();
      $descendants = $children->toArray();

      foreach ($children as $key => $child)
      {
        $descendants[$key]['__descendants'] = $child->getDescendants();
      }

      return $descendants;
    }
  }

  public function __adjancy_list_getParent()
  {
    return $this->getTable()->find($this->getParentId());
  }

  public function __adjancy_list_getLevel()
  {
    return count($this->__adjancy_list_getAncestors());
  }

  public function __adjancy_list_isRoot()
  {
    return null === $this->getParentId();
  }

  public function __adjancy_list_isLeaf()
  {
    return (0 === count($this->getChildren()));
  }

  public function __adjancy_list_insertAsLastChildOf($other_node)
  {
    $this->setParentId($other_node->getId());
    $this->setRoot($other_node->getRoot());
    $this->save();
  }

  public function __adjancy_list_moveAsLastChildOf($other_node)
  {
    $this->setParentId($other_node->getId());
    $this->setRoot($other_node->getRoot());
    $this->save();
  }

  public function delete(Doctrine_Connection $conn = null)
  {
    $medias = Doctrine::getTable('mmMedia')->findBymmMediaFolderId($this->getId());

    foreach ($medias as $media)
    {
      $media->delete($conn);
    }

    $subfolders = $this->getNode()->getChildren();

    foreach ($subfolders as $subfolder)
    {
      $subfolder->delete($conn);
    }

    $filesystem = mediatorMediaLibraryToolkit::getFilesystem();
    $sizes = mediatorMediaLibraryToolkit::getAvailableSizes();
    $path = $this->getAbsolutePath();

    foreach ($sizes as $size => $params)
    {
      $filesystem->unlink($params['directory'].DIRECTORY_SEPARATOR.$path);
    }

    parent::delete($conn);
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
        ->from('mmMediaFolderMetadata m')
        ->where('m.name = ? AND mm_media_folder_id = ?', array($name, $this->id))
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

  public function hasMetadata($name)
  {
    return (null !== $this->getMetadata($name));
  }

  public function save(Doctrine_Connection $conn = null)
  {
    if ($this->isNew())
    {
      if ($this->getName() && !$this->getFolderPath())
      {
        $this->setFolderPath(Doctrine::getTable('mmMediaFolder')->generateFolderName($this->getName()));
      }

      if (!sfConfig::get('app_mediatorMediaLibraryPlugin_use_nested_set', false) && $this->parent_id)
      {
        // ensure the absolute path is coherent with the parent and the folder_path
        $parent = ('mmMediaFolder' == is_object($this->parent_id)get_class($this->parent_id) && ) ? $this->parent_id : Doctrine::getTable('mmMediaFolder')->findOneById($this->parent_id);

        if (!$parent)
        {
          throw new sfException('This parent does not exist.');
        }

        $parent_path = ('' != $parent->getAbsolutePath()) ? $parent->getAbsolutePath().DIRECTORY_SEPARATOR : '';
        $this->setAbsolutePath($parent_path.$this->getFolderPath());

        // create the filesystem
        $filesystem = mediatorMediaLibraryToolkit::getFilesystem();
        $sizes = mediatorMediaLibraryToolkit::getAvailableSizes();

        // create the associated folders
        foreach ($sizes as $size => $params)
        {
          if (isset($params['directory']))
          {
            $filesystem->mkdir($params['directory'].DIRECTORY_SEPARATOR.$parent_path.$this->getFolderPath());
          }
        }
      }
    }

    parent::save($conn);

    if (isset($this->_metadatas_unsaved))
    {
      foreach ($this->_metadatas_unsaved as $key => $value)
      {
        $this->setMetadata($key, $value);
      }
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

    if (null === $metadata)
    {
      $metadata = new mmMediaFolderMetadata();
      $metadata->setName($name);
      $metadata->setmmMediaFolderId($this->getPrimaryKey());
    }

    $metadata->setValue($value);
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

  public function update($array)
  {
    // create the filesystem
    $filesystem = mediatorMediaLibraryToolkit::getFilesystem();

    $previous_parent = $this->getNode()->getParent();
    $parent = $previous_parent;
    $previous_absolute_path = $this->getAbsolutePath();
    $descendants = $this->getNode()->getDescendants();

    foreach ($array as $key => $value)
    {
      if ('parent' !== $key)
      {
        $fieldNames = Doctrine::getTable('mmMediaFolder')->getColumnNames();

        if (in_array($key, $fieldNames))
        {
          $this->$key = $value;
        }
        else
        {
          $this->setMetadata($key, $value);
        }
      }
    }

    $sizes = mediatorMediaLibraryToolkit::getAvailableSizes();
    $folder_path = isset($array['folder_path']) ? $array['folder_path'] : mediatorMediaLibraryInflector::toUrl($this->getName());
    $this->setFolderPath($folder_path);

    if (isset($array['parent']))
    {
      $parent = $array['parent'];
      $parent_path = ('' != $parent->getAbsolutePath()) ? $parent->getAbsolutePath().DIRECTORY_SEPARATOR : '';
      $this->setAbsolutePath($parent_path.$folder_path);

      if ($this->isNew())
      {
        foreach ($sizes as $size => $params)
        {
          if (isset($params['directory']))
          {
            $filesystem->mkdir($params['directory'].DIRECTORY_SEPARATOR.$parent_path.$folder_path);
          }
        }
      }
      else
      {
        if ($this->getAbsolutePath() != $previous_absolute_path)
        {
          // physically move the folders on the disk
          foreach ($sizes as $size => $params)
          {
            if (isset($params['directory']))
            {
              $filesystem->rename(
                $params['directory'].DIRECTORY_SEPARATOR.$previous_absolute_path,
                $params['directory'].DIRECTORY_SEPARATOR.$parent_path.$folder_path
              );
            }
          }

          // update the descendants paths
          $this->moveDescendant($descendants, $parent_path, $folder_path, $previous_absolute_path);
        }
      }

      if (!$previous_parent)
      {
        $this->getNode()->insertAsLastChildOf($parent);
      }
      else
      {
        $this->getNode()->moveAsLastChildOf($parent);
      }
    }
  }

  public function getFiles()
  {
    $q = Doctrine_Query::create()->from('mmMedia m')
      ->where('m.mm_media_folder_id = ?', $this->getId());
    return $q->execute();
  }

  public function moveDescendant($descendants, $parent_path, $folder_path, $previous_absolute_path)
  {
    foreach ($descendants as $d)
    {
      $descendant = Doctrine::getTable('mmMediaFolder')->find($d['id']);
      $descendant_absolute_path = $descendant->getAbsolutePath();
      $descendant->setAbsolutePath($parent_path.$folder_path.substr($descendant_absolute_path, strlen($previous_absolute_path)));
      $descendant->save();

      if (isset($d['__descendants']))
      {
        $this->moveDescendant($d['__descendants'], $parent_path, $folder_path, $previous_absolute_path);
      }
    }
  }
}