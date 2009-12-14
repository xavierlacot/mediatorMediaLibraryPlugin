<?php
class mmMediaTagForm extends sfFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'   => new sfWidgetFormInputHidden(),
      'name' => new sfWidgetFormJQueryAutocompleter(array('url' => $this->getOption('url'))),
    ));

    $this->setValidators(array(
      'id'   => new sfValidatorDoctrineChoice(array('model' => 'mmMedia', 'column' => 'id')),
      'name' => new sfValidatorString(array('max_length' => 250, 'required' => true)),
    ));

    $this->widgetSchema->setNameFormat('mm_media_tag[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    parent::setup();
  }

  public function doSave($con = null)
  {
    $this->getObject()->addTag($this->values['name']);
    $this->getObject()->save();
  }

  public function getModelName()
  {
    return 'mmMedia';
  }
}