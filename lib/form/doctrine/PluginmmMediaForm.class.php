<?php

/**
 * PluginmmMedia form.
 *
 * @package    form
 * @subpackage mmMedia
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginmmMediaForm extends BasemmMediaForm
{
  public function setup()
  {
    if ($this->getObject()->isNew())
    {
      $this->setWidgets(array(
        'file'               => new sfWidgetFormInputFile(),
        'mm_media_folder_id' => new sfWidgetFormInputHidden(),
      ));

      $this->setValidators(array(
        'file'               => new sfValidatorFile(),
        'mm_media_folder_id' => new sfValidatorDoctrineChoice(array('model' => 'mmMediaFolder', 'column' => 'id', 'required' => true)),
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

    $this->widgetSchema->setNameFormat('mm_media[%s]');
  }

  public function doSave($con = null)
  {
    $user_id = sfContext::getInstance()->getUser()->getAttribute('user_id', null, 'sfGuardSecurityUser');
    $mm_media_folder = Doctrine::getTable('mmMediaFolder')->find($this->values['mm_media_folder_id']);

    if (is_null($mm_media_folder))
    {
      throw new sfException('Could not retrieve containing folder.');
    }

    $file = $this->getValue('file');

    $fields = array(
      'mm_media_folder' => $mm_media_folder,
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
      '/mediatorMediaLibraryPlugin/js/mootools.js',
      '/mediatorMediaLibraryPlugin/js/fancyupload/Swiff.Uploader.js',
      '/mediatorMediaLibraryPlugin/js/fancyupload/Fx.ProgressBar.js',
      '/mediatorMediaLibraryPlugin/js/mootools.Lang.js',
      '/mediatorMediaLibraryPlugin/js/fancyupload/FancyUpload2.js'
    );
  }

  public function getStylesheets()
  {
    return array(
      '/mediatorMediaLibraryPlugin/css/fancyupload.css' => 'all',
    );
  }
}