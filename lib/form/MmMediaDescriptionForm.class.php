<?php

class MmMediaDescriptionForm extends BaseMmMediaForm
{
  public function setup()
  {
    $this->setWidgets(array(
      'body'  => new sfWidgetFormTextarea(),
      'tags'  => new mediatorWidgetFormAutoSuggest(array('url' => $this->getOption('url'))),
      'title' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'body'  => new sfValidatorString(array('required' => false)),
      'tags'  => new sfValidatorString(array('required' => false)),
      'title' => new sfValidatorString(array('required' => false)),
    ));

    $this->setDefault('tags', implode(', ', $this->getObject()->getTags()));
    $this->widgetSchema->setNameFormat('mm_media[%s]');
  }

  public function doSave($con = null)
  {
    parent::doSave($con);
    $this->getObject()->setTags(strtolower($this->values['tags']));
    $this->getObject()->save();
  }
}