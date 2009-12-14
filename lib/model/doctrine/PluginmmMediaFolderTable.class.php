<?php
/**
 */
class PluginmmMediaFolderTable extends Doctrine_Table
{
  public function __call($method, $arguments)
  {
    $methods = array(
      'createRoot',
      'fetchRoots',
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

  public function getTree()
  {
    if (!sfConfig::get('app_mediatorMediaLibraryPlugin_use_nested_set', false))
    {
      return $this;
    }
  }

  public function __adjancy_list_createRoot($node)
  {
    $node->setParentId(null);
    return $node->save();
  }

  public function __adjancy_list_fetchRoots()
  {
    $q = Doctrine_Query::create()
      ->from('mmMediaFolder m')
      ->where('m.parent_id IS NULL');
    return $q->execute();
  }
}