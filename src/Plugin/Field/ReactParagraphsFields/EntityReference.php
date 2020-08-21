<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Entity reference field plugin.
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
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
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

    if (isset($field_element['widget']['media_library_selection'])) {
      $this->addMediaLibraryInfo($info, $field_config);
    }
    return $info;
  }

  /**
   * When the widget is a media library, add data to the field info.
   *
   * @param array $info
   *   Field info data array.
   * @param \Drupal\field\FieldConfigInterface $field_config
   *   Media library field.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function addMediaLibraryInfo(array &$info, FieldConfigInterface $field_config) {
    $info['widget_type'] = 'media_library';
    $bundles = $field_config->getSetting('handler_settings')['target_bundles'] ?? [];
    $target_bundles = $this->entityTypeManager->getStorage('media_type')
      ->loadMultiple($bundles);
    $info['target_bundles'] = array_map(function ($bundle) {
      return $bundle->label();
    }, $target_bundles);
  }

}
