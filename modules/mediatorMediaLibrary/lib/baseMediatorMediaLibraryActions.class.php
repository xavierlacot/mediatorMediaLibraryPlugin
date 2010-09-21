<?php

class baseMediatorMediaLibraryActions extends sfActions
{
  public function executeAdd(sfWebRequest $request)
  {
    sfConfig::set('sf_web_debug', false);
    set_time_limit( sfConfig::get('app_mediatorMediaLibraryPlugin_max_upload_time', 300));
    $this->retrieveFolder($request);
    $this->form = new mmMediaForm();
    $this->form->setDefaults(array('mm_media_folder_id' => $this->mm_media_folder->getId()));

    if ($request->isMethod('post'))
    {
      $this->form->bind(
        $request->getParameter($this->form->getName()),
        $request->getFiles($this->form->getName())
      );

      if ($this->form->isValid())
      {
        $this->form->doSave();
        $uuid = $this->form->getObject()->getUuid();

        if (!$uuid)
        {
          throw new sfException('Impossible upload: incorrect unique id.');
        }

        if (false === strpos($_SERVER['HTTP_USER_AGENT'], 'Flash'))
        {
          $this->getUser()->setFlash('notice', 'The file has been uploaded successfully.');
          $this->redirect('@mediatorMediaLibrary_describe?media_ids='.$uuid);
        }
        else
        {
          $this->getResponse()->setContent($uuid);
          return sfView::NONE;
        }
      }
      else
      {
        $message = '';

        foreach ($this->form->getErrorSchema() as $field => $error)
        {
          $message .= sprintf(' %s: %s.', $field, $error->getMessage());
        }

        throw new sfException('Impossible upload:'.$message);
      }
    }
  }

  public function executeAddTag(sfWebRequest $request)
  {
    if ($request->isMethod('post'))
    {
      $mm_media_tag = $request->getParameter('mm_media_tag');
      $this->mm_media = Doctrine::getTable('mmMedia')->find($mm_media_tag['id']);
      $form = new mmMediaTagForm($this->mm_media);
      $form->bind($mm_media_tag);

      if ($form->isValid())
      {
        $form->doSave();
      }
    }

    $this->setTemplate('tagList');
  }

  public function executeChoose(sfWebRequest $request)
  {
    $this->retrieveFolder($request);

    $this->allowed_types = array(
      mediatorMedia::META_TYPE_SOUND => (bool) $this->getRequestParameter("audio", true),
      mediatorMedia::META_TYPE_IMAGE => (bool) $this->getRequestParameter("image", true),
      mediatorMedia::META_TYPE_VIDEO => (bool) $this->getRequestParameter("video", true),
      mediatorMedia::META_TYPE_OTHER => (bool) $this->getRequestParameter("other", true)
    );

    // if no restrictions, no hidden field.
    if (!array_search(false, $this->allowed_types))
    {
      $this->allowed_types = false;
    }
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->retrieveFile();
    $path = $this->mm_media->getmmMediaFolder()->getAbsolutePath();
    $this->mm_media->delete();
    $this->getUser()->setFlash('notice', 'The file has been deleted successfully.');
    $this->redirect('mediatorMediaLibrary/list?path='.$path);
  }

  public function executeDeleteTag(sfWebRequest $request)
  {
    if ($request->isMethod('post'))
    {
      $tagging = $request->getParameter('tagging');
      $this->mm_media = Doctrine::getTable('mmMedia')->find($tagging[0]);
      $this->mm_media->removeTag($tagging[1]);
      $this->mm_media->save();
    }

    $this->setTemplate('tagList');
  }

  public function executeDescribe(sfWebRequest $request)
  {
    $this->autocomplete_url = $this->getController()->genUrl('mediatorMediaLibrary/tagAutosuggest');
    $this->retrieveFiles();
    $this->form = new MmMultiMediaDescriptionForm(
      $this->medias,
      array('url' => $this->autocomplete_url, 'sf_user' => $this->getUser())
    );

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $this->form->save();
        $path = $this->medias[0]->getmmMediaFolder()->getAbsolutePath();

        if ($request->isXmlHttpRequest())
        {
          $this->redirect('mediatorMediaLibrary/choose?path='.$path);
          $request->setParameter('path', $path);
          $this->forward('mediatorMediaLibrary', 'choose', array('path' => $path));
        }
        else
        {
          $this->getUser()->setFlash('notice', 'The files have been saved.');
          $this->redirect('mediatorMediaLibrary/list?path='.$path);
        }
      }
    }
  }

  public function executeDetail(sfWebRequest $request)
  {
    $this->retrieveFile();
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->retrieveFile();
    $formClass = 'mmMedia'.ucfirst($this->mm_media->getType()).'Form';

    if (class_exists($formClass))
    {
      $this->form = new $formClass($this->mm_media);

      if ($request->isMethod('post'))
      {
        $mm_media = $this->getRequestParameter('mm_media_'.$this->mm_media->getType());
        $this->form->bind($mm_media);

        if ($this->form->isValid())
        {
          $this->form->doSave();
          $this->getUser()->setFlash('notice', 'The media has been successfully edited.');
          $this->redirect('mediatorMediaLibrary/view?path='.$this->mm_media->getAbsoluteFilename());
        }
      }
    }
    else
    {
      $this->forward404('This media type doesn\'t support the "edit" action.');
    }
  }

  public function executeFolderAdd(sfWebRequest $request)
  {
    $this->retrieveFolder($request);
    $this->form = new mmMediaFolderForm();
    $this->form->setDefaults(array('parent' => $this->mm_media_folder->getPrimaryKey()));

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('mm_media_folder'));

      if ($this->form->isValid())
      {
        $this->form->doSave();
        $path = $this->form->getObject()->getAbsolutePath();

        if ($request->isXmlHttpRequest())
        {
          $this->redirect('mediatorMediaLibrary/choose?path='.$path);
        }
        else
        {
          $this->getUser()->setFlash('notice', 'The folder has been created successfully.');
          $this->redirect('mediatorMediaLibrary/list?path='.$path);
        }
      }
    }
  }

  public function executeFolderDelete(sfWebRequest $request)
  {
    $this->retrieveFolder($request);
    $parent = $this->mm_media_folder->getNode()->getParent();
    $this->mm_media_folder->delete();
    $this->redirect('mediatorMediaLibrary/list?path='.$parent->getAbsolutePath());
  }

  public function executeFolderEdit(sfWebRequest $request)
  {
    $this->retrieveFolder($request);

    if ($request->isMethod('put'))
    {
      $request_parameter = $request->getParameter('mm_media_folder');
      $this->mm_media_folder = Doctrine::getTable('mmMediaFolder')->find($request_parameter['id']);
      $this->form = new mmMediaFolderForm($this->mm_media_folder);
      $this->form->bind($request_parameter);

      if ($this->form->isValid())
      {
        $this->form->doSave();
        $this->getUser()->setFlash('notice', 'The folder has been modified successfully.');
        $this->redirect('mediatorMediaLibrary/list?path='.$this->form->getObject()->getAbsolutePath());
      }
    }
    else
    {
      $this->form = new mmMediaFolderForm($this->mm_media_folder);
    }
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('mediatorMediaLibrary', 'list');
  }

  public function executeList(sfWebRequest $request)
  {
    if ($request->isXmlHttpRequest())
    {
      $this->forward('mediatorMediaLibrary', 'choose');
    }

    $this->retrieveFolder($request);
  }

  public function executeMedias(sfWebRequest $request)
  {
    $size = $request->getParameter('size');

    try
    {
      $this->retrieveFile();
    }
    catch (mediatorFileNotFoundException $e) { }

    if ($this->mm_media && ('original' == $size))
    {
      // read the local cache file
      header('Cache-Control: public');
      header('Content-Disposition: attachment; filename="'.$this->mm_media->getFilename().'"');
      header('Content-Type: '.$this->mm_media->getMimeType());
      readfile($this->mm_media->getMediatorMedia()->cache());
    }
    else
    {
      // get the file content from the filesystem
      $fs = mediatorMediaLibraryToolkit::getFilesystem();
      $path = $size.'/'.$this->getRequestParameter('path', '');

      if ($fs->exists($path))
      {
        header('Cache-Control: public');
        $pathinfo = pathinfo($path);

        $mimeType = mediatorMedia::getMimeTypeFromFileExtension($pathinfo['extension']);

        header('Content-Type: video/ogg');
        header('Content-Disposition: attachment; filename="'.$pathinfo['basename'].'"');
        echo $fs->read($path);
      }
      else
      {
        $this->forward404();
      }
    }

    return sfView::NONE;
  }

  public function executeMove(sfWebRequest $request)
  {
    $this->retrieveFile();
    $formClass = 'mmMediaMoveForm';
    $this->form = new $formClass($this->mm_media);

    if ($request->isMethod('post'))
    {
      $mm_media = $this->getRequestParameter('mm_media');
      $this->form->bind($mm_media);

      if ($this->form->isValid())
      {
        $this->form->doSave();
        $this->getUser()->setFlash('notice', 'The media has been successfully moved.');
        $this->redirect('mediatorMediaLibrary/view?path='.$this->form->getObject()->getAbsoluteFilename());
      }
    }
  }

  public function executeSearch(sfWebRequest $request)
  {
    $form = $request->getParameter('mm_media_tag');
    $this->tag = $form['name'];

    if ($this->tag)
    {
      $this->files = PluginTagTable::getObjectTaggedWith($this->tag, array('model' => 'mmMedia'));
      $this->directories = PluginTagTable::getObjectTaggedWith($this->tag, array('model' => 'mmMediaFolder'));
    }
  }

  /**
   * Return a json array with the most popular tags
   *
   * @param sfWebRequest $request
   * @return json
   */
  public function executeTagAutocomplete(sfWebRequest $request)
  {
    $tags_array = array();
    $tags = $this->retrievePopularTags($request);

    foreach ($tags as $name => $weight)
    {
      $tags_array[$name] = $name;
    }

    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($tags_array));
  }

  /**
   * Return a json array with the most popular tags, formatted in an other way
   *
   * @param sfWebRequest $request
   * @return json
   */
  public function executeTagAutosuggest(sfWebRequest $request)
  {
    $tags_array = array();
    $tags = $this->retrievePopularTags($request);

    foreach ($tags as $name => $weight)
    {
      $tags_array[] = array(
        'value' => $name,
        'name' => $name,
      );
    }

    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($tags_array));
  }

  public function executeView(sfWebRequest $request)
  {
    $this->retrieveFile();
  }

  protected function retrieveFile()
  {
    $requested_path = $this->getRequestParameter('path', '');
    $this->mm_media = Doctrine::getTable('mmMedia')->findByFilename($requested_path);

    if (!$this->mm_media)
    {
      throw new mediatorFileNotFoundException(sprintf('Could not retrieve this file : "%s".', $requested_path));
    }
  }

  protected function retrieveFiles()
  {
    $this->uuids = $this->getRequestParameter('media_ids', null);
    $requested_ids = explode(',', $this->uuids);
    $this->forward404Unless(count($requested_ids) > 0 && ($requested_ids[0] != ''));

    $this->medias = Doctrine_Query::create()
      ->from('mmMedia m')
      ->whereIn('m.uuid', $requested_ids)
      ->execute();

    if (count($this->medias) == 0)
    {
      throw new mediatorFileNotFoundException(sprintf('Could not retrieve the files %s.', implode(', ', $requested_ids)));
    }
  }

  protected function retrieveFolder(sfWebRequest $request)
  {
    $requested_path = $request->getParameter('path', '');
    $this->mm_media_folder = Doctrine::getTable('mmMediaFolder')->findOneByAbsolutePath($requested_path);

    if (!$this->mm_media_folder)
    {
      if ('' === $requested_path)
      {
        throw new mediatorFileNotFoundException('Could not retrieve the root directory. You must first initialize the plugin using the task media:initialize');
      }
      else
      {
        throw new mediatorFileNotFoundException(sprintf('Could not retrieve this directory : "%s".', $requested_path));
      }
    }
  }

  protected function retrievePopularTags(sfWebRequest $request)
  {
    $tags = TagTable::getPopulars(
      null,
      array(
        'like'  => '%'.$request->getParameter('q').'%',
        'limit' => $request->getParameter('limit', 10),
      )
    );
    arsort($tags);
    return $tags;
  }
}
