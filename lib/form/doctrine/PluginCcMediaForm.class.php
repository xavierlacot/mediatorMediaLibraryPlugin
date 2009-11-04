<?php

/**
 * PluginCcMedia form.
 *
 * @package    form
 * @subpackage CcMedia
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginCcMediaForm extends BaseCcMediaForm
{
  public function setup()
  {
    if ($this->getObject()->isNew())
    {
      $this->setWidgets(array(
        'file'               => new sfWidgetFormInputFile(),
        'cc_media_folder_id' => new sfWidgetFormInputHidden(),
      ));

      $this->setValidators(array(
        'file'               => new sfValidatorFile(),
        'cc_media_folder_id' => new sfValidatorDoctrineChoice(array('model' => 'ccMediaFolder', 'column' => 'id', 'required' => true)),
      ));
    }
    else
    {
      $this->setWidgets(array(
        'file' => new sfWidgetFormInputFile(),
        'body' => new sfWidgetFormTextarea(),
      ));

      $this->setValidators(array(
        'file' => new sfValidatorFile(),
        'body' => new sfValidatorString(array('required' => false)),
      ));
    }

    $this->widgetSchema->setNameFormat('cc_media[%s]');
  }

  public function doSave($con = null)
  {
    $user_id = sfContext::getInstance()->getUser()->getAttribute('user_id', null, 'sfGuardSecurityUser');
    $cc_media_folder = Doctrine::getTable('ccMediaFolder')->find($this->values['cc_media_folder_id']);

    if (is_null($cc_media_folder))
    {
      throw new sfException('Could not retrieve containing folder.');
    }

    $file = $this->getValue('file');

    $fields = array(
      'cc_media_folder' => $cc_media_folder,
      'source'          => $file->getTempName(),
      'filename'        => $file->getOriginalName(),
      'updated_by'      => $user_id,
    );

    if ($this->getObject()->isNew())
    {
      $fields['created_by'] = $user_id;
    }

    $this->getObject()->update($fields);
  }

  public function getJavaScripts()
  {
    return array(
      '/cleverMediaLibraryPlugin/js/jquery.uploadify/swfobject.js',
      '/cleverMediaLibraryPlugin/js/jquery.uploadify/jquery.uploadify.v2.1.0.min.js',
    );
  }

  public function getStylesheets()
  {
    return array(
      '/cleverMediaLibraryPlugin/js/jquery.uploadify/uploadify.css',
    );
  }
}