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
 * @FormElement("internet_service_provider")
 *
 * @see \Drupal\webform\Element\WebformCompositeBase
 * @see \Drupal\webform_example_composite\Element\WebformExampleComposite
 */
class InternetServiceProvider extends WebformCompositeBase {

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
    $provider_ids = array('' => '- Select -', 'Spectrum' => 'Spectrum', 'Cox' => 'Cox', 'AT&T' => 'AT&T');
    $elements['provider_id'] = [
      '#type' => 'webform_select_other',
      '#title' => 'Internet Service Provider',
      '#options' => $provider_ids,
      // Use #after_build to add #states.
      '#after_build' => [[get_called_class(), 'afterBuild']],
      '#other__placeholder' => 'Other Provider',
      '#attributes' => [
        'class' => [
          'w-100 flex room-id',
        ]
      ],
    ];

    return $elements;
  }

  /**
   * Performs the after_build callback.
   */
  public static function afterBuild(array $element, FormStateInterface $form_state) {
    if($form_state->isSubmitted() == false){
      $element['#disabled'] = false;
    }

    // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
    // disabling the entire table row when this element is disabled.
    $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
    return $element;
  }
}
