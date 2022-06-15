<?php

namespace Drupal\net_flow\Element;

use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'AssignedBy'.
 *
 * Webform elements are just wrappers around form elements, therefore every
 * webform element must have correspond FormElement.
 *
 * Below is the definition for a custom 'AssignedBy' which just
 * renders a simple text field.
 *
 * @FormElement("assignedby")
 *
 * @see \Drupal\Core\Render\Element\FormElement
 * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Element%21FormElement.php/class/FormElement
 * @see \Drupal\Core\Render\Element\RenderElement
 * @see https://api.drupal.org/api/drupal/namespace/Drupal%21Core%21Render%21Element
 * @see \Drupal\webform_example_element\Element\WebformExampleElement
 */
class AssignedBy extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#size' => 60,
      '#process' => [
        [$class, 'processWebformAssignedBy'],
        [$class, 'processAjaxForm'],
      ],
      '#element_validate' => [
        [$class, 'validateWebformAssignedBy'],
      ],
      '#pre_render' => [
        [$class, 'preRenderWebformAssignedBy'],
      ],
      '#theme' => 'input__webform_example_element',
      '#theme_wrappers' => ['form_element'],
    ];
  }

  /**
   * Processes a 'AssignedBy' element.
   */
  public static function processWebformAssignedBy(&$element, FormStateInterface $form_state, &$complete_form) {
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $user_name = $user->get('field_first_name')->getValue()[0]['value'] . ' ' .
      $user->get('field_last_name')->getValue()[0]['value'];

    if($user_name === null){
      $element['#value'] = $user->get('name')->getValue()[0]['value'];
    }
    $element['#value'] = $user_name;
    return $element;
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
