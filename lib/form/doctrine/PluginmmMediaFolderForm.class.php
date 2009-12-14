<?php

/**
 * PluginmmMediaFolder form.
 *
 * @package    form
 * @subpackage mmMediaFolder
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginmmMediaFolderForm extends BasemmMediaFolderForm
{
  public function setup()
  {
    if (is_null($this->getObject()->getFolderPath()))
    {
      $this->setWidgets(array(
        'name'   => new sfWidgetFormInput(),
        'parent' => new sfWidgetFormInputHidden(),
      ));

      $this->setValidators(array(
        'name'   => new sfValidatorString(array('max_length' => 200, 'required' => true)),
        'parent' => new sfValidatorDoctrineChoice(array('model' => 'mmMediaFolder', 'column' => 'id', 'required' => true)),
      ));
    }
    else
    {
      $this->setWidgets(array(
        'id'          => new sfWidgetFormInputHidden(),
        'name'        => new sfWidgetFormInput(),
        'folder_path' => new sfWidgetFormInput(),
        'parent'      => new sfWidgetFormDoctrineChoice(array('model' => 'mmMediaFolder', 'method' => '__toStringWithDepth')),
        'auto_path'   => new sfWidgetFormInputCheckbox(),
      ));

      $this->setValidators(array(
        'id'          => new sfValidatorDoctrineChoice(array('model' => 'mmMediaFolder', 'column' => 'id', 'required' => false)),
        'name'        => new sfValidatorString(array('max_length' => 250, 'required' => true)),
        'folder_path' => new sfValidatorString(array('max_length' => 250, 'required' => true)),
        'parent'      => new sfValidatorDoctrineChoice(array('model' => 'mmMediaFolder', 'column' => 'id', 'required' => true)),
        'auto_path'   => new sfValidatorBoolean(array('required' => false)),
      ));

      $name = $this->getObject()->getName();
      $folder_path = $this->getObject()->getFolderPath();
      $this->setDefault('auto_path', $folder_path == mediatorMediaLibraryInflector::toUrl($name));
      $this->setDefault('parent', $this->getObject()->getNode()->getParent()->getPrimaryKey());
    }

    $this->widgetSchema->setNameFormat('mm_media_folder[%s]');
  }

  public function doSave($con = null)
  {
    $parent = Doctrine::getTable('mmMediaFolder')->find($this->values['parent']);
    $user_id = sfContext::getInstance()->getUser()->getAttribute('user_id', null, 'sfGuardSecurityUser');

    if (is_null($parent))
    {
      throw new sfException('Could not retrieve parent!');
    }

    if ($this->getObject()->isNew())
    {
      $fields = array(
        'name'       => $this->values['name'],
        'created_by' => $user_id,
        'updated_by' => $user_id,
        'parent'     => $parent,
      );
    }
    else
    {
      $fields = array(
        'name'       => $this->values['name'],
        'updated_by' => $user_id,
        'parent'     => $parent,
      );

      if (!$this->values['auto_path'])
      {
        $fields['folder_path'] = $this->values['folder_path'];
      }
    }

    $this->getObject()->update($fields);
  }
}