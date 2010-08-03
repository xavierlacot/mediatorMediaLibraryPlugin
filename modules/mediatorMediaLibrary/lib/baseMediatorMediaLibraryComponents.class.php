<?php

class baseMediatorMediaLibraryComponents extends sfComponents
{
  public function executeBreadcrumb()
  {
    if (is_null($this->mm_media))
    {
      throw new Exception('The breadcrumb component needs to be passed a "mm_media" parameter.');
    }

    $folder = $this->mm_media->getmmMediaFolder();
    $this->path = $folder->getNode()->getAncestors();
    $this->path[] = $folder;
  }

  public function executeFolder_breadcrumb()
  {
    if (is_null($this->mm_media_folder))
    {
      throw new Exception('The folder_breadcrumb component needs to be passed a "mm_media_folder" parameter.');
    }

    $folder = $this->mm_media_folder;
    $this->path = $folder->getNode()->getAncestors();
    $this->path[] = $folder;
  }

  public function executeList()
  {
    // get the subdirectories
    $this->directories = $this->mm_media_folder->getNode()->getChildren();

    if (!$this->directories)
    {
      $this->directories = array();
    }

    // get the medias
    $this->files = $this->mm_media_folder->getFiles();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->search_form = new CcSearchForm(null, array('url' => $this->getController()->genUrl('mediatorMediaLibrary/tagAutocomplete')));

    $form = $request->getParameter('mm_media_tag');
    $tag = $form['name'];

    if ($tag)
    {
      $this->search_form->setDefaults(array('name' => $tag));
    }
  }

  public function executeView_tags()
  {
    $this->tags_form = new mmMediaTagForm($this->mm_media, array('url' => $this->getController()->genUrl('mediatorMediaLibrary/tagAutocomplete')));
  }
}
