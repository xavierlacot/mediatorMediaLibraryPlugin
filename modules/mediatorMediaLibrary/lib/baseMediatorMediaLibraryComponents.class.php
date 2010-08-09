<?php

class baseMediatorMediaLibraryComponents extends sfComponents
{
  public function executeAdd(sfWebRequest $request)
  {
    $types = array();
    $media_types = sfConfig::get('app_mediatorMediaLibraryPlugin_media_types', array());

    if (isset($this->allowed_types) && $this->allowed_types)
    {
      foreach ($this->allowed_types as $type => $allowed_extensions)
      {
        if ($allowed_extensions && isset($media_types[$type]))
        {
          $types[$type] = $allowed_extensions;
        }
      }
    }
    else
    {
      $types = $media_types;
    }

    $this->fileExt  = implode(';', $types);
    $this->fileDesc = implode(', ', array_keys($types));
    $this->form = new mmMediaForm();
    $this->form->setDefaults(array('mm_media_folder_id' => $this->mm_media_folder->getId()));
  }

  public function executeBreadcrumb(sfWebRequest $request)
  {
    if (is_null($this->mm_media))
    {
      throw new Exception('The breadcrumb component needs to be passed a "mm_media" parameter.');
    }

    $folder = $this->mm_media->getmmMediaFolder();
    $this->path = $folder->getNode()->getAncestors();
    $this->path[] = $folder;
  }

  public function executeFolder_breadcrumb(sfWebRequest $request)
  {
    if (is_null($this->mm_media_folder))
    {
      throw new Exception('The folder_breadcrumb component needs to be passed a "mm_media_folder" parameter.');
    }

    $folder = $this->mm_media_folder;
    $this->path = $folder->getNode()->getAncestors();
    $this->path[] = $folder;
  }

  public function executeList(sfWebRequest $request)
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

  public function executeView_tags(sfWebRequest $request)
  {
    $this->tags_form = new mmMediaTagForm($this->mm_media, array('url' => $this->getController()->genUrl('mediatorMediaLibrary/tagAutocomplete')));
  }
}
