<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;

/**
 * Number field plugin.
 *
 * @ReactParagraphsFields(
 *   id = "number",
 *   field_types = {
 *     "decimal",
 *     "float",
 *     "integer"
 *   }
 * )
 */
class Number extends ReactParagraphsFieldsBase {

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $info = parent::getFieldInfo($field_element, $field_config);
    $info['precision'] = $field_config->getFieldStorageDefinition()->getSetting('precision');
    $info['scale'] = $field_config->getFieldStorageDefinition()->getSetting('scale');
    $info['min'] = $field_config->getSetting('min');
    $info['max'] = $field_config->getSetting('max');

    $this->moduleHandler->invokeAll("react_paragraphs_getfieldinfo_alter", [$field_element, $field_config,  $info]);
    return $info;
  }

}
