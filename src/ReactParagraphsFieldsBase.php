<?php

namespace Drupal\react_paragraphs;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\field\FieldConfigInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ReactParagraphsFieldsBase
 *
 * @package Drupal\react_paragraphs
 */
abstract class ReactParagraphsFieldsBase extends PluginBase implements ContainerFactoryPluginInterface, ReactParagraphsFieldsInterface {

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $widget_type = $this->getPluginId();
    if ($widget_type == 'default') {
      $widget_type = $field_config->getFieldStorageDefinition()->getType();
    }
    return [
      'label' => $field_config->getLabel(),
      'cardinality' => $field_config->getFieldStorageDefinition()
        ->getCardinality(),
      'widget_type' => $widget_type,
      'help' => $field_config->getDescription(),
      'required' => $field_config->isRequired(),
      'weight' => $field_element['#weight'] ?? 0,
    ];

  }

}
