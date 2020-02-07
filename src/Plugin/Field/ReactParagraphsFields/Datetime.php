<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;

/**
 * Datetime and daterange field plugin.
 *
 * @ReactParagraphsFields(
 *   id = "datetime",
 *   field_types = {
 *     "datetime",
 *     "daterange"
 *   }
 * )
 */
class Datetime extends ReactParagraphsFieldsBase {

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $info = parent::getFieldInfo($field_element, $field_config);
    $info['type'] = $field_config->getFieldStorageDefinition()
      ->getSetting('datetime_type');
    return $info;
  }

}
