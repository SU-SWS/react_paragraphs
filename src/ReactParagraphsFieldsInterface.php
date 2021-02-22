<?php

namespace Drupal\react_paragraphs;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\field\FieldConfigInterface;

/**
 * Interface ReactParagraphsFieldsInterface
 *
 * @package Drupal\react_paragraphs
 */
interface ReactParagraphsFieldsInterface extends PluginInspectionInterface{

  /**
   * Get the data to pass to the react UI.
   *
   * @param array $field_element
   *   Field render array on the form.
   * @param \Drupal\field\FieldConfigInterface $field_config
   *   Field config entity.
   *
   * @return array
   *   Structured array data for the react widget.
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config);

}
