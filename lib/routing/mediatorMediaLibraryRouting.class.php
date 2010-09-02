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

    $actions = array('delete', 'detail', 'edit', 'list', 'move', 'view');

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

    // route for serving the media through php
    if (sfConfig::get('app_mediatorMediaLibraryPlugin_php_serve_media', false))
    {
      $r->prependRoute(
        'mediatorMediaLibrary_medias',
        new sfRoute(
          '/mediatorMediaLibrary/medias/:size/:path',
          array('module' => 'mediatorMediaLibrary', 'action' => 'medias'),
          array('path' => '^(.)*$')
        )
      );
    }
  }
}
