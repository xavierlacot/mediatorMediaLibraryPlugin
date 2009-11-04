<?php

class cleverMediaSessionStorage extends sfSessionStorage
{
  public function initialize($options = null)
  {
    $context = sfContext::getInstance();

    if ('cleverMediaLibrary/add' === $context->getRouting()->getCurrentInternalUri())
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