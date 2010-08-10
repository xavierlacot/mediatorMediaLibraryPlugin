<?php
$options = array('size' => isset($size) ? $size : 'original');
echo image_tag(
  $mm_media->getUrl($options),
  is_object($html_options) ? $html_options->getRawValue() : $html_options
);