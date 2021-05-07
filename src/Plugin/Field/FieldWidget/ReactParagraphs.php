<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\react_paragraphs\Entity\ParagraphsRowInterface;

/**
 * Plugin implementation of the 'react_paragraphs' widget.
 *
 * @FieldWidget(
 *   id = "react_paragraphs",
 *   label = @Translation("React Paragraphs"),
 *   field_types = {
 *     "entity_reference_revisions"
 *   },
 *   multiple_values = true
 * )
 */
class ReactParagraphs extends ReactParagraphsWidgetBase {

  /**
   * List of previously load entities.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface[]
   */
  protected $rowData = [];

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['resizable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Resizable items'),
      '#description' => $this->t('Each item can be resized to make customized widths.'),
      '#default_value' => $this->getSetting('resizable'),
    ];
    $form['sizes'] = [
      '#type' => 'details',
      '#title' => $this->t('Item Maximum Sizes'),
    ];
    foreach ($this->getTools() as $tool) {
      $form['sizes'][$tool['id']] = [
        '#type' => 'select',
        '#title' => $this->t('Maximum Columns for @label', ['@label' => $tool['label']]),
        '#default_value' => $this->getSetting('sizes')[$tool['id']] ?? 1,
        '#options' => [
          12 => 12,
          6 => 6,
          4 => 4,
          3 => 3,
          2 => 2,
          1 => 1,
        ],
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->getSetting('resizable') ? $this->t('Resizable') : $this->t('Equal Widths'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element['container'] = $element;
    $element['container'] += [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => ['react-paragraphs-fieldset'],
      ],
    ];

    // Find unique elements for the widget react container and input field.
    $element_id = Html::getUniqueId('react-' . $this->fieldDefinition->getName());

    // Get all the attachments for any CKEditor fields that could exist.
    $attachments = $this->editorManager->getAttachments(array_keys(filter_formats($this->currentUser)));
    $attachments['library'][] = 'react_paragraphs/field_widget';

    // Set the javascript settings to be picked up by react.
    $attachments['drupalSettings']['reactParagraphs'][] = [
      'fieldId' => $element_id,
      'inputId' => "$element_id-input",
      'rowBundle' => self::getRowItemsField($this->fieldDefinition)
        ->getTargetBundle(),
      'tools' => $this->getTools(),
      'items' => $this->getRowItems($items),
      'itemsPerRow' => self::getRowItemsField($this->fieldDefinition)
        ->getFieldStorageDefinition()
        ->getCardinality(),
      'resizableItems' => (bool) $this->getSetting('resizable'),
    ];

    // The hidden input with a empty container nearby for react to attach to.
    $element['container']['value'] = $element;
    $element['container']['value'] += [
      '#prefix' => '<div id="' . $element_id . '"></div>',
      '#type' => 'hidden',
      '#attached' => $attachments,
      '#attributes' => [
        'id' => "$element_id-input",
        'data-cookie-id' => $this->fieldDefinition->getUniqueIdentifier(),
      ],
    ];
    return $element;
  }

  /**
   * Get the list of rows with the nested row item data.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   List of row field values.
   *
   * @return array
   *   Keyed data of rows and items.
   */
  protected function getRowItems(FieldItemListInterface $items): array {
    return $this->getRowItemsObject($items->referencedEntities());
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    if (!empty($this->rowData)) {
      return $this->rowData;
    }
    $react_data = json_decode(rawurldecode($values['container']['value']), TRUE);

    $return_data = [];

    // Nothing was added to the rows so there's nothing to do.
    if (empty($react_data['rowOrder'])) {
      return $return_data;
    }
    // We'll use this later, it's the field on the row to save the items in.
    $items_field_name = self::getRowItemsField($this->fieldDefinition)
      ->getName();

    // Loop through the rows in the order they appear from react.
    foreach ($react_data['rowOrder'] as $row_id) {
      $row_data = $react_data['rows'][$row_id];
      $row_data['entity'][$items_field_name] = [];

      // The row is empty, we can skip it.
      if (empty($row_data['itemsOrder'])) {
        continue;
      }

      // Loop through the items within the row in the order they come. Build
      // the item entities, and save the field values to be set on the row
      // entity next.
      foreach ($row_data['itemsOrder'] as $item_id) {
        $item = $row_data['items'][$item_id];
        $item_entity = $this->getRowItemEntity($item['entity'], $item['width'], $item['admin_title'], $item['target_id'] ?? NULL);
        $row_data['entity'][$items_field_name][] = [
          'entity' => $item_entity,
          'target_id' => $item_entity->id(),
          'target_revision_id' => $item_entity->getRevisionId(),
        ];
      }

      $row = $this->getRowEntity($row_data['entity'], $row_data['target_id'] ?? NULL);
      $return_data[] = [
        'entity' => $row,
        'target_id' => $row->id(),
        'target_revision_id' => $row->getRevisionId(),
      ];
    }
    $this->rowData = $return_data;
    if ($return_data) {
      setcookie($react_data['cookieId'], $this->getRowItemsObject($return_data, TRUE), time() + 5);
    }
    return $this->rowData;
  }

  /**
   * Get the nested array of items from the existing entities.
   *
   * @param array $rows
   *   Row entities as the values, or as a value of the array.
   * @param bool $as_json
   *   Return as json data.
   *
   * @return array|string
   *   Array or json string of the array data.
   */
  protected function getRowItemsObject(array $rows, $as_json = FALSE) {
    $row_item_field = self::getRowItemsField($this->fieldDefinition);
    $all_items = [];

    // Loop through the rows that are referenced and load its field data.
    foreach ($rows as $row_delta => $row) {
      $entity = $row instanceof ParagraphsRowInterface ? $row : $row['entity'];
      $all_items[$row_delta]['row'] = [
        'target_id' => $entity->id(),
        'entity' => ['type' => [['target_id' => $entity->bundle()]]],
      ];

      // In case a row has 0 items.
      $all_items[$row_delta]['rowItems'] = [];

      /** @var \Drupal\paragraphs\ParagraphInterface $row_item */
      // Loop through the items within the row and populate the data.
      $ref_items = $entity->get($row_item_field->getName())
        ->referencedEntities();
      foreach ($ref_items as $item_delta => $row_item) {
        $all_items[$row_delta]['rowItems'][$item_delta] = [
          'target_id' => $row_item->id(),
          'entity' => ['type' => [['target_id' => $row_item->bundle()]]],
          'settings' => [
            'width' => $row_item->getBehaviorSetting('react', 'width'),
            'admin_title' => $row_item->getBehaviorSetting('react', 'label'),
          ],
        ];
      }

      // Reset the data so that its a clean array.
      $all_items[$row_delta]['rowItems'] = array_values($all_items[$row_delta]['rowItems']);
    }
    return $as_json ? json_encode($all_items) : $all_items;
  }

  /**
   * Load or create a new paragraphs row entity.
   *
   * @param array $field_data
   *   Array of field data from the react side.
   * @param int|null $entity_id
   *   Entity id if its existing.
   *
   * @return \Drupal\react_paragraphs\Entity\ParagraphsRowInterface
   *   Row entity with set field values.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getRowEntity(array $field_data, ?int $entity_id): ParagraphsRowInterface {
    /** @var \Drupal\react_paragraphs\Entity\ParagraphsRowInterface $entity */
    $entity = $this->getEntity('paragraph_row', $this->getRowBundle(), $field_data, $entity_id);
    $entity->save();
    return $entity;
  }

  /**
   * Load or create a paragraphs row item entity with field data.
   *
   * @param array $field_data
   *   Array of field data from the react side.
   * @param int|null $width
   *   Width of the current item.
   * @param string|null $admin_label
   *   Administrative label.
   * @param int|null $entity_id
   *   Entity id.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   Entity with modified field values.
   */
  protected function getRowItemEntity(array $field_data, ?int $width, ?string $admin_label, ?int $entity_id): ParagraphInterface {
    $row_item = $this->getEntity('paragraph', $field_data['type'][0]['target_id'], $field_data, $entity_id);
    $row_item->setBehaviorSettings('react', [
      'width' => $width,
      'label' => $admin_label,
    ]);
    $row_item->save();
    return $row_item;
  }

  /**
   * Get the targeted paragraphs row bundle id.
   *
   * @return string
   *   Bundle ID.
   */
  protected function getRowBundle(): string {
    $settings = $this->fieldDefinition->getSetting('handler_settings');
    return reset($settings['target_bundles']);
  }

  /**
   * Load or create an entity with some field data populated on it.
   *
   * @param string $entity_type
   *   Entity type id.
   * @param string $bundle
   *   Entity bundle id.
   * @param array $field_data
   *   Array of field data from the react side.
   * @param int|null $entity_id
   *   Entity ID if available.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   Entity with modified field values.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getEntity(string $entity_type, string $bundle, array $field_data, ?int $entity_id = NULL): ParagraphInterface {
    if (!$entity_id) {
      return $this->createEntity($entity_type, $bundle, $field_data);
    }

    /** @var \Drupal\paragraphs\ParagraphInterface $entity */
    $entity = $this->entityTypeManager->getStorage($entity_type)
      ->load($entity_id);
    $entity->setNewRevision();

    $fields = $this->fieldManager->getFieldDefinitions($entity_type, $bundle);
    foreach ($fields as $field_name => $field_definition) {
      if (isset($field_data[$field_name]) && $field_definition instanceof FieldConfigInterface) {
        $entity->set($field_name, $field_data[$field_name]);
      }
    }

    $this->setBehaviorSettings($entity, $field_data['behavior_settings'] ?? []);
    return $entity;
  }

  /**
   * Create a new entity with the give field data.
   *
   * @param string $entity_type
   *   Entity type ID.
   * @param string $bundle
   *   Bundle ID.
   * @param array $field_data
   *   Array of field data from the react side.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   Newly created entity.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function createEntity(string $entity_type, string $bundle, array $field_data): ParagraphInterface {
    $entity_definition = $this->entityTypeManager->getDefinition($entity_type);
    $bundle_key = $entity_definition->getKey('bundle');
    $storage = $this->entityTypeManager->getStorage($entity_type);

    $entity_values = [$bundle_key => $bundle];
    $fields = $this->fieldManager->getFieldDefinitions($entity_type, $bundle);
    foreach ($fields as $field_name => $field_definition) {
      if (isset($field_data[$field_name]) && $field_definition instanceof FieldConfigInterface) {
        $entity_values[$field_name] = $field_data[$field_name];
      }
    }
    /** @var \Drupal\paragraphs\ParagraphInterface $entity */
    $entity = $storage->create($entity_values);
    $this->setBehaviorSettings($entity, $field_data['behavior_settings'] ?? []);

    // TODO: Handle validation.
    $entity->validate();
    return $entity;
  }

  /**
   * Set the entity's behavior settings based on the values from the UI.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $entity
   *   Created or edited paragraph/row entity.
   * @param array $behaviors
   *   Keyed array of behavior values.
   */
  protected function setBehaviorSettings(ParagraphInterface $entity, array $behaviors) {
    if (!empty($behaviors[0]['value'])) {
      foreach ($behaviors[0]['value'] as $plugin_id => $settings) {
        $entity->setBehaviorSettings($plugin_id, $settings);
      }
    }
  }

}
