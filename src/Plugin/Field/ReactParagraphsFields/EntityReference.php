<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Text
 *
 * @ReactParagraphsFields(
 *   id = "entity_reference",
 *   field_types = {
 *     "entity_reference",
 *     "entity_reference_revisions"
 *   }
 * )
 */
class EntityReference extends ReactParagraphsFieldsBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $info = parent::getFieldInfo($field_element, $field_config);

    if (isset($field_element['widget']['media_library_selection'])) {
      $this->addMediaLibraryInfo($info, $field_element, $field_config);
    }
    return $info;
  }

  protected function addMediaLibraryInfo(&$info, $field_element, FieldConfigInterface $field_config) {
    $info['widget_type'] = 'media_library';
    $target_bundles = $this->entityTypeManager->getStorage('media_type')
      ->loadMultiple($field_config->getSetting('handler_settings')['target_bundles']);
    $info['target_bundles'] = array_map(function ($bundle) {
      return $bundle->label();
    }, $target_bundles);

  }

}
