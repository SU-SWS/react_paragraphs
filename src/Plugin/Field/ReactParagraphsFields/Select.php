<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Select field plugin.
 *
 * @ReactParagraphsFields(
 *   id = "select",
 *   field_types = {
 *     "list_float",
 *     "list_integer",
 *     "list_string",
 *     "webform"
 *   }
 * )
 */
class Select extends ReactParagraphsFieldsBase {

  /**
   * Entity Type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('entity_type.manager'));
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $info = parent::getFieldInfo($field_element, $field_config);

    $info['widget_type'] = $field_element['widget']['#type'] ?? $info['widget_type'] ;

    $options = $field_config->getFieldStorageDefinition()
      ->getSetting('allowed_values');

    $info['column_key'] = 'value';
    if ($field_config->getType() == 'webform') {
      /** @var \Drupal\webform\WebformEntityStorage $webform_storage */
      $webform_storage = $this->entityTypeManager->getStorage('webform');
      $options = $webform_storage->getOptions(FALSE);
      $info['column_key'] = 'target_id';
    }
    $info['options'] = $options;

    return $info;
  }

}
