<?php

function cml_format_filesize($filesize)
{
  $symbol = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
  $exp = 0;
  $converted_value = 0;

  if ($filesize > 0)
  {
    $exp = floor(log($filesize) / log(1024));
    $converted_value = ($filesize / pow(1024, floor($exp)));
  }

  if ($exp == 0)
  {
    return sprintf('%s %s', $filesize, $symbol[$exp]);
  }
  else
  {
    return sprintf('%.2f %s', $converted_value, $symbol[$exp]);
  }
}

function cml_link_to($text, $uri, $parameters = null)
{
  // grab the "path" parameter
  $params = sfContext::getInstance()->getController()->convertUrlStringToParameters($uri);

  if (!isset($params[1]['path']))
  {
    throw new sfException('the cml_link_to() helper requires a "path" parameter.');
  }

  $route = sprintf('@%s', $params[0]);
  $params_string = '';
  $path = $params[1]['path'];
  $params[1]['path'] = 'PLACEHOLDER';

  foreach ($params[1] as $key => $value)
  {
    $params_string .= ('' == $params_string) ? '?' : '&';
    $params_string .= sprintf('%s=%s', $key, $value);
  }

  // generate a link with a placeholder inside
  $link = link_to($text, $route.$params_string, $parameters);

  // replace the placeholder
  return str_replace('PLACEHOLDER', $path, $link);
}

function cml_display_media($mm_media, $options = array())
{
  $options['mm_media'] = $mm_media;
  $type = $mm_media->getType();
  $partial = 'mediatorMediaLibrary/media_types/view'.(('' != $type) ? '_'.$type : '');
  return get_partial($partial, $options);
}