<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;

/**
 * Text field plugin.
 *
 * @ReactParagraphsFields(
 *   id = "text",
 *   field_types = {
 *     "text",
 *     "string",
 *     "string_long",
 *     "email"
 *   }
 * )
 */
class Text extends ReactParagraphsFieldsBase {

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $info = parent::getFieldInfo($field_element, $field_config);
    $info['max_length'] = $field_config->getFieldStorageDefinition()
      ->getSetting('max_length') ?: 0;

    $info['formatted'] = FALSE;
    switch ($field_config->getType()) {
      case 'email':
        $info['text_type'] = 'email';
        break;

      case 'string_long':
        $info['text_type'] = 'textarea';
        break;

      default:
        $info['text_type'] = 'text';
        break;
    }
    return $info;
  }

}
