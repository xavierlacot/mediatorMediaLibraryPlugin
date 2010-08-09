<?php

class MmMultiMediaDescriptionForm extends sfForm
{
  public function __construct($objects = null, $options = array(), $CSRFSecret = null)
  {
    if (!$objects)
    {
      throw new Exception('Could not create form without objects');
    }
    else
    {
      $this->objects = array();

      foreach ($objects as $object)
      {
        $this->objects[$object->getId()] = $object;
      }
    }

    parent::__construct(array(), $options, $CSRFSecret);
  }

  protected function doSave($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    if (!isset($values))
    {
      $values = $this->values;
    }
    $this->updateObjectEmbeddedForms($values);
    $this->saveEmbeddedForms($con);
  }

  /**
   * @return Doctrine_Connection
   * @see sfFormObject
   */
  public function getConnection()
  {
    return Doctrine_Manager::getInstance()->getConnectionForComponent($this->getModelName());
  }

  protected function getMediaDescriptionFormClass()
  {
    return 'MmMediaDescriptionForm';
  }

  protected function getModelName()
  {
    return 'MmMedia';
  }

  public function getObject($id)
  {
    if (isset($this->objects[$id]))
    {
      return $this->objects[$id];
    }
    else
    {
      throw new Exception(sprintf('Unknown object %s.', $i));
    }
  }

  /**
   * Saves the current object to the database.
   * The object saving is done in a transaction and handled by the doSave() method.
   *
   * @param mixed $con An optional connection object
   * @return mixed The current saved objects
   * @see doSave()
   *
   * @throws sfValidatorError If the form is not valid
   */
  public function save($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    try
    {
      $con->beginTransaction();

      $this->doSave($con);

      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();

      throw $e;
    }

    return $this->objects;
  }

  /**
   * Saves embedded form objects.
   *
   * @param mixed $con   An optional connection object
   * @param array $forms An array of forms
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    if (null === $forms)
    {
      $forms = $this->embeddedForms;
    }

    foreach ($forms as $form)
    {
      if ($form instanceof sfFormObject)
      {
        $form->saveEmbeddedForms($con);
        $form->getObject()->save($con);
      }
      else
      {
        $this->saveEmbeddedForms($con, $form->getEmbeddedForms());
      }
    }
  }

  public function setup()
  {
    $media_description_form_class = $this->getMediaDescriptionFormClass();

    foreach ($this->objects as $id => $object)
    {
      if (!$object instanceof MmMedia)
      {
        throw new sfException(sprintf('The "%s" form only accepts a "%s" object.', get_class($this), $this->getModelName()));
      }

      $media_description_form = new $media_description_form_class($object, $this->options);
      $this->embedForm('media_'.$id, $media_description_form);
    }

    $this->widgetSchema->setNameFormat('mm_media_description[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    parent::setup();
  }

  /**
   * Updates the values of the objects in embedded forms.
   *
   * @param array $values An array of values
   * @param array $forms  An array of forms
   */
  public function updateObjectEmbeddedForms($values, $forms = null)
  {
    if (null === $forms)
    {
      $forms = $this->embeddedForms;
    }

    foreach ($forms as $name => $form)
    {

      if (!isset($values[$name]) || !is_array($values[$name]))
      {
        continue;
      }

      if ($form instanceof sfFormObject)
      {
        if (array_key_exists('tags',$values[$name]))
        {
          $values[$name]['tags'] = strToLower($values[$name]['tags']);
        }

        $form->updateObject($values[$name]);
      }
      else
      {
        $this->updateObjectEmbeddedForms($values[$name], $form->getEmbeddedForms());
      }
    }
  }
}