<?php

/**
 * Routing configuration
 *
 * @package    mediatorMediaLibraryRouting
 * @subpackage routing
 * @author     Xavier Lacot <xavier@lacot.org>
 */
class mediatorMediaLibraryRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();
    $r->prependRoute(
      'mediatorMediaLibrary',
      new sfRoute(
        '/mediatorMediaLibrary/:action/:path',
        array('module' => 'mediatorMediaLibrary'),
        array('path' => '^(.)*$')
      )
    );

    $r->prependRoute(
      'mediatorMediaLibrary_describe',
      new sfRoute(
        '/mediatorMediaLibrary/describe/:media_ids',
        array('module' => 'mediatorMediaLibrary', 'action' => 'describe'),
        array('media_ids' => '^(.)*$')
      )
    );

    $actions = array('delete', 'edit', 'list', 'move', 'view');

    foreach ($actions as $action)
    {
      $r->prependRoute(
        'mediatorMediaLibrary_'.$action,
        new sfRoute(
          '/mediatorMediaLibrary/'.$action.'/:path',
          array('module' => 'mediatorMediaLibrary', 'action' => $action),
          array('path' => '^(.)*$')
        )
      );
    }
  }
}
