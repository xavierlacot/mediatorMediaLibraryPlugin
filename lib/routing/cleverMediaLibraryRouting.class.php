<?php

/**
 * Routing configuration
 *
 * @package    cleverMediaLibraryRouting
 * @subpackage routing
 * @author     Xavier Lacot <xavier@lacot.org>
 */
class cleverMediaLibraryRouting
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
      'cleverMediaLibrary',
      new sfRoute(
        '/cleverMediaLibrary/:action/:path',
        array('module' => 'cleverMediaLibrary'),
        array('path' => '^(.)*$')
      )
    );

    $actions = array('delete', 'edit', 'list', 'view');

    foreach ($actions as $action)
    {
      $r->prependRoute(
        'cleverMediaLibrary_'.$action,
        new sfRoute(
          '/cleverMediaLibrary/'.$action.'/:path',
          array('module' => 'cleverMediaLibrary', 'action' => $action),
          array('path' => '^(.)*$')
        )
      );
    }
  }
}
