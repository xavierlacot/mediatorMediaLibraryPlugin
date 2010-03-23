<?php
class mmMediaMoveForm extends sfFormDoctrine
{
  protected function getChoices()
  {
    if (!($nodes = Doctrine::getTable('mmMediaFolder')->getTree()->fetchRoots()))
    {
      throw new sfException('The media library has not been initialized!');
    }

    $root = $nodes[0];
    $nodes = $root->getDescendants();
    return $this->addChilds(array($root->getId() => $root->getName()), $nodes, 1);
  }

  protected function addChilds($choices, $childs, $level)
  {
    foreach ($childs as $child)
    {
      $choices[$child['id']] = str_repeat("&nbsp;&nbsp;&nbsp;", $level).$child['name'];
      $choices = $this->addChilds($choices, $child['__descendants'], $level + 1);
    }

    return $choices;
  }

  public function setup()
  {
    $choices = $this->getChoices();
    $this->setWidgets(array(
      'id' => new sfWidgetFormInputHidden(),
      'mm_media_folder_id' => new sfWidgetFormSelect(array(
        'choices' => $choices,
      ))
    ));

    $this->setValidators(array(
      'id' => new sfValidatorDoctrineChoice(array('model' => 'mmMedia', 'column' => 'id', 'required' => true)),
      'mm_media_folder_id' => new sfValidatorDoctrineChoice(array(
        'model' => 'mmMediaFolder',
        'column' => 'id',
        'required' => true
      ))
    ));

    $this->widgetSchema['mm_media_folder_id']->setLabel('Move to folder');
    $this->widgetSchema->setNameFormat('mm_media[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    parent::setup();
  }

  public function doSave($con = null)
  {
    $user_id = sfContext::getInstance()->getUser()->getAttribute('user_id', null, 'sfGuardSecurityUser');
    $mm_media_folder = Doctrine::getTable('mmMediaFolder')->find($this->values['mm_media_folder_id']);

    if (is_null($mm_media_folder))
    {
      throw new sfException('Could not retrieve containing folder.');
    }

    $fields = array(
      'mm_media_folder' => $mm_media_folder,
      'updated_by'      => $user_id,
    );
    $this->getObject()->moveTo($fields);
  }

  public function getModelName()
  {
    return 'mmMedia';
  }
}