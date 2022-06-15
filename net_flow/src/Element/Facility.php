<?php

namespace Drupal\net_flow\Element;

use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Select;

/**
 * Provides a 'webform_example_element'.
 *
 * Webform elements are just wrappers around form elements, therefore every
 * webform element must have correspond FormElement.
 *
 * Below is the definition for a custom 'webform_example_element' which just
 * renders a simple text field.
 *
 * @FormElement("facility")
 *
 * @see \Drupal\Core\Render\Element\FormElement
 * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Element%21FormElement.php/class/FormElement
 * @see \Drupal\Core\Render\Element\RenderElement
 * @see https://api.drupal.org/api/drupal/namespace/Drupal%21Core%21Render%21Element
 * @see \Drupal\webform_example_element\Element\WebformExampleElement
 */
class Facility extends Select {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $properties = parent::getInfo();
    $class = get_class($this);
    $properties['#process'][] = [$class, 'processMyCustomSelect'];

    return $properties;
  }

  public static function processAjaxForm(&$element, FormStateInterface $form_state, &$complete_form)
  {

    return parent::processAjaxForm($element, $form_state, $complete_form); // TODO: Change the autogenerated stub
  }

  /**
   * Processes a 'webform_example_element' element.
   */
  public static function processMyCustomSelect(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['#empty_value'] = "";
    $element['#empty_option'] = t('-Select-');
    $facility = array();
    $mca = $form_state->getValue('current_medical_area');

    if($complete_form["elements"]["net_new_process_information"]["current_medical_area"]["#value"]){
      $mca = $complete_form["elements"]["net_new_process_information"]["current_medical_area"]["#value"];
    }

    if($mca){
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
        ->loadByProperties(['name' => $mca, 'vid' => 'medical_area']);
      $term = reset($term);
      if($term != false){
        $term_id = $term->id();
        $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('medical_area', $term_id, 1);
        foreach ($terms as $term) {
          $facility[$term->name] = $term->name;
        }
        if(!in_array($element['#value'], $facility)){
          $element['#value'] = "";
        }
      }
    }
    $element['#options'] = $facility;
    $element['#prefix'] = '<div id="current-facility" class="d-flex col-12 col-md-5 mt-auto p-0 js-form-item">';
    $element['#suffix'] = '</div>';
    $element = parent::processSelect($element, $form_state, $complete_form);
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

    Element::setAttributes($element, ['id', 'name', 'value', 'placeholder']);
    static::setAttributes($element, ['form-text', 'current-facility']);
    return $element;
  }

}