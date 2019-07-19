<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;

/**
 * Class Text
 *
 * @ReactParagraphsFields(
 *   id = "view",
 *   field_types = {
 *     "viewfield"
 *   }
 * )
 */
class View extends ReactParagraphsFieldsBase {

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $info = parent::getFieldInfo($field_element, $field_config);
    $info['options'] = [];
    return $info;
  }

}
