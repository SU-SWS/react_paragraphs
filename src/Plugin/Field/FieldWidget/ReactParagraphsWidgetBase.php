<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\editor\Plugin\EditorManager;
use Drupal\field\FieldConfigInterface;
use Drupal\paragraphs\ParagraphsTypeInterface;
use Drupal\paragraphs\Plugin\EntityReferenceSelection\ParagraphSelection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ReactParagraphsWidgetBase for the field widget.
 *
 * @package Drupal\react_paragraphs\Plugin\Field\FieldWidget
 */
abstract class ReactParagraphsWidgetBase extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Selection plugin manager service.
   *
   * @var \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface
   */
  protected $selectionManager;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Editor manager service.
   *
   * @var \Drupal\editor\Plugin\EditorManager
   */
  protected $editorManager;

  /**
   * Field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldManager;

  /**
   * Current account object.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('plugin.manager.entity_reference_selection'),
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.editor'),
      $container->get('entity_field.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, SelectionPluginManagerInterface $selection_manager, EntityTypeManagerInterface $entity_type_manager, EditorManager $editor_manager, EntityFieldManagerInterface $field_manager, AccountProxyInterface $current_user) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->selectionManager = $selection_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->editorManager = $editor_manager;
    $this->fieldManager = $field_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $settings['resizable'] = FALSE;
    $settings['sizes'] = [];
    return $settings;
  }

  /**
   * Get the field on the paragraphs row that will hold the items.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   Paragraphs row field definition.
   *
   * @return \Drupal\field\FieldConfigInterface|null
   *   Field on the paragraphs row.
   */
  protected static function getRowItemsField(FieldDefinitionInterface $field_definition) {
    $target_bundles = $field_definition->getSetting('handler_settings')['target_bundles'];
    /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager */
    $field_manager = \Drupal::service('entity_field.manager');
    $fields = $field_manager->getFieldDefinitions('paragraph_row', reset($target_bundles));

    // Grab the first field that is a paragraphs reference field. There should
    // never be more than one. Maybe we can provide configurable options later.
    foreach ($fields as $field) {
      if (
        $field instanceof FieldConfigInterface &&
        $field->getType() == 'entity_reference_revisions' &&
        $field->getSetting('handler') == 'default:paragraph'
      ) {
        return $field;
      }
    }
  }

  /**
   * Get the url of the paragraph types icon if it exists.
   *
   * @param \Drupal\paragraphs\ParagraphsTypeInterface $type
   *   Paragraphs type entity.
   *
   * @return string|null
   *   Path to icon or null if non exists..
   */
  protected static function getParagraphTypeIcon(ParagraphsTypeInterface $type) {
    try {
      return $type->getIconUrl() ?: NULL;
    }
    catch (\Exception $e) {
      \Drupal::logger('react_paragraphs')
        ->error('Unable to get paragraph icon for %type', ['%type' => $type->id()]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // First the field must be targeting the paragraphs row entity type & only
    // one bundle chosen.
    if (
      $field_definition->getSetting('handler') == 'default:paragraph_row' &&
      count($field_definition->getSetting('handler_settings')['target_bundles']) == 1
    ) {
      return !empty(self::getRowItemsField($field_definition));
    }
    return FALSE;
  }

  /**
   * Get the available paragraph types that are allowed in this field.
   *
   * @return array
   *   Keyed array of tool options with label and icon urls.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getTools() {
    $return_bundles = [];
    $row_item_field = self::getRowItemsField($this->fieldDefinition);
    $handler = $this->selectionManager->getSelectionHandler($row_item_field);

    // Get a list of paragraph types that are allowed in the current field.
    if (!$handler instanceof ParagraphSelection) {
      throw new \Exception('Invalid field');
    }

    $field_bundles = $handler->getSortedAllowedTypes();

    // Load the paragraph types to check for icons.
    $bundle_entities = $this->entityTypeManager->getStorage('paragraphs_type')
      ->loadMultiple(array_keys($field_bundles));
    $sizes = $this->getSetting('sizes');
    /** @var \Drupal\paragraphs\ParagraphsTypeInterface $paragraph_type */
    foreach ($bundle_entities as $id => $paragraph_type) {
      $return_bundles[] = [
        'id' => $id,
        'label' => $paragraph_type->label(),
        'description' => $paragraph_type->getDescription(),
        'icon' => self::getParagraphTypeIcon($paragraph_type),
        'minWidth' => (int) ($sizes[$id] ?? 1),
      ];
    }

    return $return_bundles;
  }

}
