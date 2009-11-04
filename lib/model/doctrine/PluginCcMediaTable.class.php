<?php
/**
 */
class PluginCcMediaTable extends Doctrine_Table
{
  public function getLastCreated($limit = null)
  {
    $q = Doctrine_Query::create()
      ->select('m.*')
      ->from('CcMedia m')
      ->orderBy('m.created_at DESC');

    if (null !== $limit)
    {
      $q->limit($limit);
    }

    return $q->execute();
  }


  /**
   * retrieves a cc_media from its path
   *
   * @param string $filename
   * @return ccMedia
   */
  public function retrieveByFilename($path)
  {
    $path_parts = pathinfo($path);

    if ('.' == $path_parts['dirname'])
    {
      $path_parts['dirname'] = '';
    }

    $q = Doctrine_Query::create()->from('CcMedia m')
      ->innerJoin('m.CcMediaFolder f')
      ->where('m.filename = ?', $path_parts['basename'])
      ->andWhere('f.absolute_path = ?', $path_parts['dirname']);
    $result = $q->execute();

    return isset($result[0]) ? $result[0] : null;
  }

  public function retrieveByPk($pk)
  {
    $q = Doctrine_Query::create()->from('CcMedia m')
      ->where('m.id = ?', $pk);
    $result = $q->execute();

    return isset($result[0]) ? $result[0] : null;
  }
}