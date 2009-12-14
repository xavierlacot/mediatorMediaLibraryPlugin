<?php
/**
 * mediatorMediaOdfJodConverterAdapter is an adapter for openoffice readable
 * documents, using OpenOffice and JobConverter
 *
 * @package    mediatorMediaOfficeJodConverterAdapter
 * @author     Xavier Lacot <xavier@lacot.org>
 */
class mediatorMediaOfficeJodConverterAdapter extends mediatorMediaAdapter
{
  public function crop($options)
  {

  }

  public function getDimensions()
  {
    return array();
  }

  public function getPagesCount()
  {
    return $this->pagesCount;
  }

  public function initialize($file, cleverFilesystem $filesystem, $options = array())
  {
    parent::initialize($file, $filesystem, $options);
    $this->mime_type = 'application/vnd.oasis.opendocument.presentation';
  }

  public function resize($maxWidth = null, $maxHeight = null, $options = array())
  {

  }

  public function toImage($options)
  {    // convert document to PNG, then resize it
    $browser = new sfWebBrowser();
    $browser->post(
      'http://localhost:8080/converter/service',
      array(
        'Body' => file_get_contents($this->cache_file)
      ),
      array(
        'Content-Type' => $this->mime_type,
        'Accept' => 'image/png'
      )
    );
    $toto = $browser->getResponseBody();
    file_put_contents('/tmp/toto.png', $toto); die();
  }
}