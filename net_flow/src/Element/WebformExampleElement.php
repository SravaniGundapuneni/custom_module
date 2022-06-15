<?php

namespace Drupal\net_flow\Element;

use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'webform_example_element'.
 *
 * Webform elements are just wrappers around form elements, therefore every
 * webform element must have correspond FormElement.
 *
 * Below is the definition for a custom 'webform_example_element' which just
 * renders a simple text field.
 *
 * @FormElement("webform_example_element")
 *
 * @see \Drupal\Core\Render\Element\FormElement
 * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Element%21FormElement.php/class/FormElement
 * @see \Drupal\Core\Render\Element\RenderElement
 * @see https://api.drupal.org/api/drupal/namespace/Drupal%21Core%21Render%21Element
 * @see \Drupal\webform_example_element\Element\WebformExampleElement
 */
class WebformExampleElement extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#size' => 60,
      '#process' => [
        [$class, 'processWebformElementExample'],
        [$class, 'processAjaxForm'],
      ],
      '#element_validate' => [
        [$class, 'validateWebformExampleElement'],
      ],
      '#pre_render' => [
        [$class, 'preRenderWebformExampleElement'],
      ],
      '#theme' => 'input__webform_example_element',
      '#theme_wrappers' => ['form_element'],
    ];
  }

  /**
   * Processes a 'webform_example_element' element.
   */
  public static function processWebformElementExample(&$element, FormStateInterface $form_state, &$complete_form) {
    //if no sys_no, we grab the latest webform submission and give the webform that value
    $sys_no = $form_state->getValue('system_no');
    $current_path = \Drupal::service('path.current')->getPath();

    if(empty($sys_no) && $current_path == "/webform/generate_system_number_form_1_"){
      $sql = 'SELECT sid FROM `webform_submission` ORDER BY sid DESC LIMIT 1';
      $query = \Drupal::database()->query($sql);
      $record = $query->fetchField(0);
      if($record === false){
        $element['#value'] = 0;
      }else{
        $element['#value'] = strval(++$record);
      }
    }
    return $element;
  }

  /**
   * Webform element validation handler for #type 'webform_example_element'.
   */
  public static function validateWebformExampleElement(&$element, FormStateInterface $form_state, &$complete_form) {
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
  public static function preRenderWebformExampleElement(array $element) {
    $element['#attributes']['type'] = 'text';
    Element::setAttributes($element, ['id', 'name', 'value', 'size', 'maxlength', 'placeholder']);
    static::setAttributes($element, ['form-text']);
    return $element;
  }

}
