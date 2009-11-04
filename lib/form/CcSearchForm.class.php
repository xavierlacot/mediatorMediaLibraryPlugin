<?php
class CcSearchForm extends sfForm
{
  public function setup()
  {
    $this->setWidgets(array(
      'name' => new sfWidgetFormJQueryAutocompleter(array('url' => $this->getOption('url'))),
    ));

    $this->setValidators(array(
      'name' => new sfValidatorString(array('max_length' => 250, 'required' => true)),
    ));

    $this->widgetSchema->setNameFormat('cc_media_tag[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    parent::setup();
  }
}