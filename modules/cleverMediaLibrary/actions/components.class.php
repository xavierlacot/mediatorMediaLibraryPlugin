<?php

class cleverMediaLibraryComponents extends sfComponents
{
  public function executeBreadcrumb()
  {
    if (is_null($this->cc_media))
    {
      throw new Exception('The breadcrumb component needs to be passed a "cc_media" parameter.');
    }

    $folder = $this->cc_media->getCcMediaFolder()->getRawValue();
    $this->path = $folder->getNode()->getAncestors();
    $this->path[] = $folder;
  }

  public function executeFolder_breadcrumb()
  {
    if (is_null($this->cc_media_folder))
    {
      throw new Exception('The folder_breadcrumb component needs to be passed a "cc_media_folder" parameter.');
    }

    $folder = $this->cc_media_folder->getRawValue();
    $this->path = $folder->getNode()->getAncestors();
    $this->path[] = $folder;
  }

  public function executeList()
  {
    // get the subdirectories
    $this->directories = $this->cc_media_folder->getRawValue()->getNode()->getChildren();

    if (!$this->directories)
    {
      $this->directories = array();
    }

    // get the medias
    $this->files = $this->cc_media_folder->getRawValue()->getFiles();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->search_form = new CcSearchForm(null, array('url' => $this->getController()->genUrl('cleverMediaLibrary/tagAutocomplete')));

    $form = $request->getParameter('cc_media_tag');
    $tag = $form['name'];

    if ($tag)
    {
      $this->search_form->setDefaults(array('name' => $tag));
    }
  }

  public function executeView_tags()
  {
    $this->tags_form = new CcMediaTagForm($this->cc_media->getRawValue(), array('url' => $this->getController()->genUrl('cleverMediaLibrary/tagAutocomplete')));
  }
}
