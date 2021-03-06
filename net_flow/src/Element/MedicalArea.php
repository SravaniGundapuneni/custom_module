<?php

namespace Drupal\net_flow\Element;

use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Select;

/**
 * Provides a 'MedicalArea'.
 *
 * Webform elements are just wrappers around form elements, therefore every
 * webform element must have correspond FormElement.
 *
 * Below is the definition for a custom 'MedicalArea' which just
 * renders a simple text field.
 *
 * @FormElement("medicalarea")
 *
 * @see \Drupal\Core\Render\Element\FormElement
 * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Element%21FormElement.php/class/FormElement
 * @see \Drupal\Core\Render\Element\RenderElement
 * @see https://api.drupal.org/api/drupal/namespace/Drupal%21Core%21Render%21Element
 * @see \Drupal\webform_example_element\Element\WebformExampleElement
 */
class MedicalArea extends Select {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $properties = parent::getInfo();
    $class = get_class($this);
    $properties['#process'] = [
      [$class, 'processSelect'],
      [$class, 'processAjaxForm'],
    ];
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function processSelect(&$element, FormStateInterface $form_state, &$complete_form) {
    $mca = array();
    $element['#empty_value'] = "";
    $element['#empty_option'] = t('-Select-');

    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('medical_area', $parent = 0, 1);
    foreach ($terms as $term) {
      $mca[$term->name] = $term->name;
    }
    $element['#options'] = $mca;

    $element = parent::processSelect($element, $form_state, $complete_form);
    return $element;
  }

  /**
   * {@inherticdoc}
   */
  public static function processAjaxForm(&$element, FormStateInterface $form_state, &$complete_form)
  {
    return parent::processAjaxForm($element, $form_state, $complete_form); // TODO: Change the autogenerated stub
  }


  /**
   * Webform element validation handler for #type 'webform_example_element'.
   */
  public static function validateWebformAssignedBy(&$element, FormStateInterface $form_state, &$complete_form) {
    // Here you can add custom validation logic.
  }

  /**
   * Prepares a #type 'email_multiple' render element for theme_element().
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   *   Properties used: #title, #value, #description, #size, #maxlength,
   *   #placeholder, #required, #attributes.
   *
   * @return array
   *   The $element with prepared variables ready for theme_element().
   */
  public static function preRenderWebformAssignedBy(array $element) {
    $element['#attributes']['type'] = 'text';
    Element::setAttributes($element, ['id', 'name', 'value', 'size', 'maxlength', 'placeholder']);
    static::setAttributes($element, ['form-text']);
    return $element;
  }

}
