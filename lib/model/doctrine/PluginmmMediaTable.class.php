<?php
/**
 */
class PluginmmMediaTable extends Doctrine_Table
{
  public function getLastCreated($limit = null)
  {
    $q = Doctrine_Query::create()
      ->select('m.*')
      ->from('mmMedia m')
      ->orderBy('m.created_at DESC');

    if (null !== $limit)
    {
      $q->limit($limit);
    }

    return $q->execute();
  }


  /**
   * retrieves a mm_media from its path
   *
   * @param string $filename
   * @return mmMedia
   */
  public function findByFilename($path)
  {
    $path_parts = pathinfo($path);

    if ('.' == $path_parts['dirname'])
    {
      $path_parts['dirname'] = '';
    }

    $q = Doctrine_Query::create()->from('mmMedia m')
      ->innerJoin('m.mmMediaFolder f')
      ->where('m.filename = ?', $path_parts['basename'])
      ->andWhere('f.absolute_path = ?', $path_parts['dirname']);
    $result = $q->execute();

    return isset($result[0]) ? $result[0] : null;
  }

  public function findByFilenameAndFolder($filename, $folderId)
  {
    $result = Doctrine_Query::create()->from('mmMedia m')
      ->where('m.filename = ?', $filename)
      ->andWhere('m.mm_media_folder_id = ?', $folderId)
      ->execute();

    return isset($result[0]) ? $result[0] : null;
  }
}
