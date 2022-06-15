<?php

namespace Drupal\net_flow\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
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
 * @FormElement("network_settings")
 *
 * @see \Drupal\webform\Element\WebformCompositeBase
 * @see \Drupal\net_flow\Element\NetworkSettings
 */
class NetworkSettings extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return parent::getInfo() + ['#theme' => 'webform_example_composite'];
  }

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements(array $element) {

    $elements = [];
    $elements['modtap'] = [
      '#type' => 'textfield',
      '#title' => t('Modtap/Label'),
      '#attributes' => ['class' => ['textComposite']],
    ];
    $elements['network_type'] = [
      '#type' => 'select',
      '#title' => t('Network Type'),
      '#options' => ['wired' => 'Wired', 'wireless' => 'Wireless'],
      '#attributes' => [ 'class' => ['selectComposite']],
    ];

    $elements['speed'] = [
      '#type' => 'select',
      '#title' => t('Speed/Duplex'),
      '#options' => ['10' => '10H', '100' => '100F', '1000' => '1000F'],
      '#attributes' => [ 'class' => ['selectComposite']],
    ];
    $elements['mac'] = [
      '#type' => 'textfield',
      '#title' => t('Mac Address'),
      '#attributes' => ['class' => ['textComposite']],
    ];
    $elements['bound_member'] = [
      '#type' => 'checkbox',
      '#title' => t('Bound Member'),
    ];
/*
    $elements['composite_ip_address'] = [
      '#type' => 'webform_custom_composite',
      '#title' => 'IP address',
      '#element' => [
        'dhcp' => [
          '#type' => 'checkbox',
          '#title' => t('DHCP'),
        ],
        'ip_address' => [
          '#type' => 'textfield',
          '#title' => 'IP Address',
          '#after_build' => [[get_called_class(), 'afterBuildSecond']],
//        '#element_validate' => [[get_called_class(), 'validateElement']],
//          '#theme' => 'input__ip_address',
//          '#theme_wrappers' => ['form_element'],
        ],
        'subnet' => [
          '#type' => 'textfield',
          '#title' => 'Subnet Mask',
          '#after_build' => [[get_called_class(), 'afterBuildSecond']],
        ],
        'gateway' => [
          '#type' => 'textfield',
          '#title' => 'Gateway',
          '#after_build' => [[get_called_class(), 'afterBuildSecond']],
        ],
        'hostname' => [
          '#type' => 'textfield',
          '#title' => 'Hostname (FQDN)',
          '#after_build' => [[get_called_class(), 'afterBuildSecond']],
        ],
      'composite_aetitle' => [
      '#type' => 'webform_custom_composite',
      '#title' => 'AE Title',
      '#element' => [
       'add_aetitle' => [
          '#type' => 'checkbox',
          '#title' => t('Add AETitle'),
          '#default_value' => FALSE,
        ],
        'aetitle' => [
          '#type' => 'textfield',
          '#title' => 'AE Title',
          '#default_value' => 'FONUS'.rand(0,79897),
          '#attributes' => [ 'class' => ['textComposite']],
          '#after_build' => [[get_called_class(), 'afterBuildFirst']],
        ],
        'aetitle_desc' => [
          '#type' => 'textfield',
          '#title' => 'AE Title Description',
          '#after_build' => [[get_called_class(), 'afterBuildFirst']],
        ],
        'aetitle_port' => [
          '#type' => 'textfield',
          '#title' => 'Port',
          '#after_build' => [[get_called_class(), 'afterBuildFirst']],
        ],
        ],
    ],
      ],
    ];
*/

    return $elements;
  }

  /**
   * Performs the after_build callback.
   */
  public static function afterBuildFirst(array $element, FormStateInterface $form_state) {

    // Add #states targeting the specific element and table row.
    preg_match('/^(.+)\[[^]]+]$/', $element['#name'], $match);
    $composite_name = $match[1];
    $element['#states']['visible'] = [
      [':input[name="' . $composite_name . '[add_aetitle]"]' => ['checked' => TRUE]],
    ];
    $element['#states']['required'] = [
      [':input[name="'. $composite_name .'[add_aetitle]"]' => ['checked' => TRUE]],

    ];
    // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
    // disabling the entire table row when this element is disabled.
    $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';

    return $element;
  }

  public static function afterBuildSecond(array $element, FormStateInterface $form_state) {

    // Add #states targeting the specific element and table row.
    preg_match('/^(.+)\[[^]]+]$/', $element['#name'], $match);
    $composite_name = $match[1];

    $isNestedComposite = strpos($composite_name, 'composite_ip_address');

    if($isNestedComposite) {
      $element['#states']['disabled'] = [
        [':input[name="'. $composite_name .'[dhcp]"]' => ['checked' => TRUE]],
      ];

//      $element['#states']['required'] = [
//        [':input[name="' . $composite_name . '[dhcp]"]' => ['checked' => FALSE]],
//      ];

      // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
      // disabling the entire table row when this element is disabled.
      $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
    }

    return $element;
  }

//  public static function afterBuildThird(array $element, FormStateInterface $form_state) {
//
//    // Add #states targeting the specific element and table row.
//    preg_match('/^(.+)\[[^]]+]$/', $element['#name'], $match);
//    $composite_name = $match[1];
//
//    $isNestedComposite = strpos($composite_name, 'composite_ip_address');
//
//    if($isNestedComposite) {
//
//      $element['#states']['required'] = [
//        [':input[name="'. $composite_name .'[dhcp]"]' => ['checked' => FALSE]],
//
//      ];
//      // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
//      // disabling the entire table row when this element is disabled.
//      $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
//    }
//
//    return $element;
//  }
  public static function validateElement(&$element, FormStateInterface $form_state, &$complete_form) {

  }

}
