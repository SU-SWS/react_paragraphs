<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Database\Connection;
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
   * Database connection service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('database')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->database = $database;
  }

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $info = parent::getFieldInfo($field_element, $field_config);

    if (isset($field_element['widget']['media_library_selection'])) {
      $this->addMediaLibraryInfo($info, $field_config);
    }

    if ($this->isSelectListType($field_element)) {
      $this->addAutocompleteInfo($info, $field_config);
    }
    return $info;
  }

  /**
   * Is the field element an autocomplete or select list type of widget.
   *
   * @param array $field_element
   *   Form field element.
   *
   * @return bool
   *   If the form element is like a select list.
   */
  protected function isSelectListType(array $field_element) {
    if (
      !empty($field_element['widget'][0]['target_id']['#type']) &&
      $field_element['widget'][0]['target_id']['#type'] == 'entity_autocomplete'
    ) {
      return TRUE;
    }

    return !empty($field_element['widget']['#type']) && $field_element['widget']['#type'] == 'select';
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

  /**
   * Add the select list or autocomplete options.
   *
   * @param array $info
   *   React data.
   * @param \Drupal\field\FieldConfigInterface $field_config
   *   Field config entity.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function addAutocompleteInfo(array &$info, FieldConfigInterface $field_config) {
    $handler = $field_config->getSetting('handler');

    // We don't want to handle view handlers at this time.
    if (strpos($handler, 'default:') !== 0) {
      return;
    }
    $handler_settings = $field_config->getSetting('handler_settings');
    $entity_type = substr($handler, strlen('default:'));
    $entity_storage = $this->entityTypeManager->getStorage($entity_type);

    $entity_definition = $this->entityTypeManager->getDefinition($entity_type);
    $bundle_key = $entity_definition->getKey('bundle');

    // Do a simple query to load all entity IDs without loading the entities.
    // Using the getQuery it will also do access checks for the current user.
    $entity_ids = $entity_storage->getQuery()
      ->condition($bundle_key, $handler_settings['target_bundles'], 'IN')
      ->execute();

    $label_key = $entity_definition->getKey('label');
    $id_key = $entity_definition->getKey('id');

    $info['options'] = [];

    // Now that we now the entity IDs and based on the entity definition, we
    // can simply gather the data straight from the database.
    $data = $this->database->select($entity_definition->getDataTable(), 'd')
      ->fields('d', [$id_key, $label_key])
      ->condition($id_key, $entity_ids, 'IN')
      ->execute()
      ->fetchAllKeyed();

    // Construct the options based on the query data.
    foreach ($data as $id => $label) {
      $info['options'][] = [
        'entityId' => $id,
        'label' => $label,
      ];
    }
    $info['widget_type'] = 'entity_reference_autocomplete';
  }

}
