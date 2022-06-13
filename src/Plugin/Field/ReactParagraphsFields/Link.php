<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Render\Element;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;

/**
 * Link field plugin.
 *
 * @ReactParagraphsFields(
 *   id = "link",
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class Link extends ReactParagraphsFieldsBase {

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $info = parent::getFieldInfo($field_element, $field_config);
    $info['title'] = $field_config->getSetting('title');

    if ($field_element['widget'][0]['uri']['#type'] == 'entity_autocomplete') {
      $route = $field_element['widget'][0]['uri']['#autocomplete_route_name'];
      $route_params = $field_element['widget'][0]['uri']['#autocomplete_route_parameters'];
      $info['autocomplete'] = Url::fromRoute($route, $route_params)->toString();
      $info['target_type'] = $route_params['target_type'];
    }
    $info['attributes'] = [];
    // Support link_attributes module.
    $attributes = $field_element['widget'][0]['options']['attributes'] ?? [];
    foreach(Element::children($attributes) as $attribute){
      $info['attributes'][$attribute] = [
        'label' => $attributes[$attribute]['#title'],
        'help' => $attributes[$attribute]['#description'],
      ];
    }
    return $info;
  }

}
