<?php
class CcMediaImageForm extends sfFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'     => new sfWidgetFormInputHidden(),
      'x1'     => new sfWidgetFormInputHidden(),
      'y1'     => new sfWidgetFormInputHidden(),
      'x2'     => new sfWidgetFormInputHidden(),
      'y2'     => new sfWidgetFormInputHidden(),
      'width'  => new sfWidgetFormInput(),
      'height' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'   => new sfValidatorDoctrineChoice(array('model' => 'ccMedia', 'column' => 'id')),
      'x1'     => new sfValidatorNumber(array('min' => 0)),
      'y1'     => new sfValidatorNumber(array('min' => 0)),
      'x2'     => new sfValidatorNumber(array('min' => 0)),
      'y2'     => new sfValidatorNumber(array('min' => 0)),
      'width'  => new sfValidatorNumber(array('min' => 0)),
      'height' => new sfValidatorNumber(array('min' => 0)),
    ));

    $this->widgetSchema->setNameFormat('cc_media_image[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    parent::setup();
  }

  public function doSave($con = null)
  {
    $media = new cleverMedia($this->getObject()->getAbsoluteFilename());
    $media->transform('crop',
                      array('x1' => $this->getValue('x1'),
                            'y1' => $this->getValue('y1'),
                            'x2' => $this->getValue('x2'),
                            'y2' => $this->getValue('y2')));
  }

  public function getModelName()
  {
    return 'ccMedia';
  }
}