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

  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config);

}
