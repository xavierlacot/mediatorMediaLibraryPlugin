<?php

class mediatorMediaLibraryActions extends sfActions
{
  public function executeAdd(sfWebRequest $request)
  {
    $this->retrieveFolder();
    $this->form = new mmMediaForm();
    $this->form->setDefaults(array('mm_media_folder_id' => $this->mm_media_folder->getId()));

    if ($request->isMethod('post'))
    {
      $this->form->bind(
        $request->getParameter('mm_media'),
        $request->getFiles('mm_media')
      );

      if ($this->form->isValid())
      {
        $this->form->doSave();
        $this->getUser()->setFlash('notice', 'The file has been uploaded successfully.');

        if (false === strpos($_SERVER['HTTP_USER_AGENT'], 'Flash'))
        {
          $this->redirect('mediatorMediaLibrary/view?path='.$this->form->getObject()->getAbsoluteFilename());
        }

        return sfView::NONE;
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

  public function executeChooseImage()
  {
    $this->retrieveFolder();
    $this->directories = $this->mm_media_folder->getNode()->getChildren();

    if (!$this->directories)
    {
      $this->directories = array();
    }

    $this->files = $this->mm_media_folder->getFiles();
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
    $this->retrieveFolder();
    $this->form = new mmMediaFolderForm();
    $this->form->setDefaults(array('parent' => $this->mm_media_folder->getPrimaryKey()));

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('mm_media_folder'));

      if ($this->form->isValid())
      {
        $this->form->doSave();
        $this->getUser()->setFlash('notice', 'The folder has been created successfully.');
        $this->redirect('mediatorMediaLibrary/list?path='.$this->form->getObject()->getAbsolutePath());
      }
    }
  }

  public function executeFolderDelete(sfWebRequest $request)
  {
    $this->retrieveFolder();
    $parent = $this->mm_media_folder->getNode()->getParent();
    $this->mm_media_folder->delete();
    $this->redirect('mediatorMediaLibrary/list?path='.$parent->getAbsolutePath());
  }

  public function executeFolderEdit(sfWebRequest $request)
  {
    $this->retrieveFolder();

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
    $this->retrieveFolder();
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

  public function executeTagAutocomplete(sfWebRequest $request)
  {
    $this->getResponse()->setContentType('application/json');
    $tags_array = array();
    $tags = TagTable::getPopulars(null,
              array(
                'like' => '%'.$request->getParameter('q').'%',
                'limit' => $request->getParameter('limit'),
              ));
    arsort($tags);

    foreach ($tags as $name => $weight)
    {
      $tags_array[$name] = $name;
    }

    return $this->renderText(json_encode($tags_array));
  }

  public function executeView(sfWebRequest $request)
  {
    $this->retrieveFile();
    $this->form = new mmMediaForm($this->mm_media);
  }

  protected function retrieveFile()
  {
    $requested_path = $this->getRequestParameter('path', '');
    $this->mm_media = Doctrine::getTable('mmMedia')->findByFilename($requested_path);

    if (!$this->mm_media)
    {
      throw new sfException(sprintf('Could not retrieve this file : "%s".', $requested_path));
    }
  }

  protected function retrieveFolder()
  {
    $requested_path = $this->getRequestParameter('path', '');
    $this->mm_media_folder = Doctrine::getTable('mmMediaFolder')->findOneByAbsolutePath($requested_path);

    if (!$this->mm_media_folder)
    {
      if ('' === $requested_path)
      {
        throw new sfException('Could not retrieve the root directory. You must first initialize the plugin using the task media:initialize');
      }
      else
      {
        throw new sfException(sprintf('Could not retrieve this directory : "%s".', $requested_path));
      }
    }
  }
}
