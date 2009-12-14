<?php

class mediatorMediaSessionStorage extends sfSessionStorage
{
  public function initialize($options = null)
  {
    $context = sfContext::getInstance();

    if ('mediatorMediaLibrary/add' === $context->getRouting()->getCurrentInternalUri())
    {
      $session_name = $options['session_name'];

      if ($value = $context->getRequest()->getParameter($session_name))
      {
        session_name($session_name);
        session_id($value);
      }
    }

    parent::initialize($options);
  }
}