<?php

class cleverMediaLibraryActions extends sfActions
{
  public function executeAdd(sfWebRequest $request)
  {
    $this->retrieveFolder();
    $this->form = new CcMediaForm();
    $this->form->setDefaults(array('cc_media_folder_id' => $this->cc_media_folder->getId()));

    if ($request->isMethod('post'))
    {
      $this->form->bind(
        $request->getParameter('cc_media'),
        $request->getFiles('cc_media')
      );

      if ($this->form->isValid())
      {
        $this->form->doSave();
        $this->getUser()->setFlash('notice', 'The file has been uploaded successfully.');

        if (false === strpos($_SERVER['HTTP_USER_AGENT'], 'Adobe Flash Player'))
        {
          $this->redirect('cleverMediaLibrary/view?path='.$this->form->getObject()->getAbsoluteFilename());
        }

        return sfView::NONE;
      }
    }
  }

  public function executeAddTag(sfWebRequest $request)
  {
    if ($request->isMethod('post'))
    {
      $cc_media_tag = $request->getParameter('cc_media_tag');
      $this->cc_media = Doctrine::getTable('CcMedia')->retrieveByPk($cc_media_tag['id']);
      $form = new CcMediaTagForm($this->cc_media);
      $form->bind($cc_media_tag);

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
    $this->directories = $this->cc_media_folder->getNode()->getChildren();

    if (!$this->directories)
    {
      $this->directories = array();
    }

    $this->files = $this->cc_media_folder->getFiles();
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->retrieveFile();
    $path = $this->cc_media->getCcMediaFolder()->getAbsolutePath();
    $this->cc_media->delete();
    $this->getUser()->setFlash('notice', 'The file has been deleted successfully.');
    $this->redirect('cleverMediaLibrary/list?path='.$path);
  }

  public function executeDeleteTag(sfWebRequest $request)
  {
    if ($request->isMethod('post'))
    {
      $tagging = $request->getParameter('tagging');
      $this->cc_media = Doctrine::getTable('CcMedia')->retrieveByPk($tagging[0]);
      $this->cc_media->removeTag($tagging[1]);
      $this->cc_media->save();
    }

    $this->setTemplate('tagList');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->retrieveFile();
    $formClass = 'CcMedia'.ucfirst($this->cc_media->getType()).'Form';

    if (class_exists($formClass))
    {
      $this->form = new $formClass($this->cc_media);

      if ($request->isMethod('post'))
      {
        $cc_media = $this->getRequestParameter('cc_media_'.$this->cc_media->getType());
        $this->form->bind($cc_media);

        if ($this->form->isValid())
        {
          $this->form->doSave();
          $this->getUser()->setFlash('notice', 'The media has been successfully edited.');
          $this->redirect('cleverMediaLibrary/view?path='.$this->cc_media->getAbsoluteFilename());
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
    $this->form = new CcMediaFolderForm();
    $this->form->setDefaults(array('parent' => $this->cc_media_folder->getPrimaryKey()));

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('cc_media_folder'));

      if ($this->form->isValid())
      {
        $this->form->doSave();
        $this->getUser()->setFlash('notice', 'The folder has been created successfully.');
        $this->redirect('cleverMediaLibrary/list?path='.$this->form->getObject()->getAbsolutePath());
      }
    }
  }

  public function executeFolderDelete(sfWebRequest $request)
  {
    $this->retrieveFolder();
    $parent = $this->cc_media_folder->getNode()->getParent();
    $this->cc_media_folder->delete();
    $this->redirect('cleverMediaLibrary/list?path='.$parent->getAbsolutePath());
  }

  public function executeFolderEdit(sfWebRequest $request)
  {
    $this->retrieveFolder();

    if ($request->isMethod('put'))
    {
      $request_parameter = $request->getParameter('cc_media_folder');
      $this->cc_media_folder = Doctrine::getTable('CcMediaFolder')->retrieveByPk($request_parameter['id']);
      $this->form = new CcMediaFolderForm($this->cc_media_folder);
      $this->form->bind($request_parameter);

      if ($this->form->isValid())
      {
        $this->form->doSave();
        $this->getUser()->setFlash('notice', 'The folder has been modified successfully.');
        $this->redirect('cleverMediaLibrary/list?path='.$this->form->getObject()->getAbsolutePath());
      }
    }
    else
    {
      $this->form = new CcMediaFolderForm($this->cc_media_folder);
    }
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('cleverMediaLibrary', 'list');
  }

  public function executeList(sfWebRequest $request)
  {
    $this->retrieveFolder();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $form = $request->getParameter('cc_media_tag');
    $this->tag = $form['name'];

    if ($this->tag)
    {
      $this->files = PluginTagTable::getObjectTaggedWith($this->tag, array('model' => 'CcMedia'));
      $this->directories = PluginTagTable::getObjectTaggedWith($this->tag, array('model' => 'CcMediaFolder'));
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
    // Building the current path
    $requested_path = $this->getRequestParameter('path', '');
    $this->cc_media = Doctrine::getTable('CcMedia')->retrieveByFilename($requested_path);

    if (!$this->cc_media)
    {
      throw new sfException(sprintf('Could not retrieve this file : "%s".', $requested_path));
    }

    $this->form = new CcMediaForm($this->cc_media);
  }

  protected function retrieveFile()
  {
    $requested_path = $this->getRequestParameter('path', '');
    $this->cc_media = Doctrine::getTable('CcMedia')->retrieveByFilename($requested_path);

    if (!$this->cc_media)
    {
      throw new sfException(sprintf('Could not retrieve this file : "%s".', $requested_path));
    }
  }

  protected function retrieveFolder()
  {
    $requested_path = $this->getRequestParameter('path', '');
    $this->cc_media_folder = Doctrine::getTable('CcMediaFolder')->retrieveByPath($requested_path);

    if (!$this->cc_media_folder)
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
