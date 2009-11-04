<?php
/**
 */
class PluginCcMediaFolderTable extends Doctrine_Table
{
  public function retrieveByPath($path)
  {
    $q = Doctrine_Query::create()->from('CcMediaFolder f')
      ->where('f.absolute_path = ?', $path);
    $result = $q->execute();

    return isset($result[0]) ? $result[0] : null;
  }

  public function retrieveByPk($pk)
  {
    $q = Doctrine_Query::create()->from('CcMediaFolder f')
      ->where('f.id = ?', $pk);
    $result = $q->execute();

    return isset($result[0]) ? $result[0] : null;
  }
}