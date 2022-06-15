<?php

namespace Drupal\net_flow\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Element\WebformCompositeBase;

/**
 * Provides a 'webform_example_composite'.
 *
 * Webform composites contain a group of sub-elements.
 *
 *
 * IMPORTANT:
 * Webform composite can not contain multiple value elements (i.e. checkboxes)
 * or composites (i.e. webform_address)
 *
 * @FormElement("architectural_room_number")
 *
 * @see \Drupal\webform\Element\WebformCompositeBase
 * @see \Drupal\webform_example_composite\Element\WebformExampleComposite
 */
class ArchitecturalRoomNumber extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return parent::getInfo() +
       [
        '#theme' => 'webform_example_composite',
         '#pre_render' => [
           [$class, 'preRenderCompositeFormElement'],
         ],
       ];
  }

  public static function preRenderCompositeFormElement($element) {
    $element['#theme_wrappers'][] = 'form_element';
    $element['#wrapper_attributes']['id'] = $element['#id'] . '--wrapper';
    $element['#wrapper_attributes']['class'][] = 'form-composite';

    $element['#attributes']['id'] = $element['#id'];

    // Add class name to wrapper attributes.
    $class_name = str_replace('_', '-', $element['#type']);
    static::setAttributes($element, ['js-' . $class_name, $class_name]);

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements(array $element) {
    $elements = [];
    $room_ids = array('' => '- Select -');

    $elements['room_id'] = [
      '#type' => 'webform_select_other',
      '#title' => 'Architectural Room Number',
      '#options' => $room_ids,
      // Use #after_build to add #states.
      '#after_build' => [[get_called_class(), 'afterBuild']],
      '#other__placeholder' => 'Other Architectural Room ID',
      '#states' => [
        'required' => [
          [':input[name="form_factor"]' => ['value' => "6"]],
        ],
      ],
      '#attributes' => [
        'class' => [
          'w-100 flex room-id',
        ],
      ],
    ];
    $read_at_home = ($_POST["read_at_home"]) ?: "empty";
    if($read_at_home === "Yes"){
      $elements['room_id']['#attributes']["class"][] = "no-pointer";
    }

    if(!empty($element) || ($_POST["_triggering_element_value"] == "Submit" && $_REQUEST["facility_name"])){
      $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('room_id');
      foreach ($terms as $term) {
        $room_ids[$term->name] = $term->name;
      }

      //if submitted and has facility name
      if($_POST["_triggering_element_value"] == "Submit" && $_REQUEST["facility_name"]){
        //facility name
        $room_ids = array('' => '- Select -');
        $term= \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($_REQUEST["facility_name"]);

        foreach ($terms as $term) {
          $term_obj = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term->tid);
          if($fac_obj = $term_obj->get('field_facility_id')[0]){
            $fac_name =  $fac_obj->getValue('target_id');
            if ($fac_name['target_id'] === $_REQUEST["facility_name"]) {
              $room_ids[$term_obj->get('name')->value] = $term_obj->get('name')->value;
            }
          }
        }
      }
    }

    if($_REQUEST["form_factor"] == '6'){
      $elements["room_id"]["#required"] = true;
    }

    $elements['room_id']['#options'] = $room_ids;
    return $elements;
  }

  /**
   * Performs the after_build callback.
   */
  public static function afterBuild(array $element, FormStateInterface $form_state) {
    // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
    // disabling the entire table row when this element is disabled.
    $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
    $readAtHome = $form_state->getUserInput()['read_at_home'];
    if($readAtHome === "Yes"){
      $element['#disabled'] = true;
      $element["#attributes"]["disabled"] =
      $element["select"]["#attributes"]["disabled"] = true;
      return $element;
    }

    if($form_state->getValue('facility_name') != null && $form_state->isSubmitted() == false){
      $element['#disabled'] = false;
    }
    return $element;
  }
}
