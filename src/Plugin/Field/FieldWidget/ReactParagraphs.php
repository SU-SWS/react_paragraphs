<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\FieldConfigInterface;

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
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    return [
      $this->getSetting('resizable') ? $this->t('Resizable') : $this->t('Equal Widths'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
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
      '#attributes' => ['id' => "$element_id-input"],
    ];
    dpm(json_encode($attachments['drupalSettings']['reactParagraphs']));
    return $element;
  }

  protected function getRowItems(FieldItemListInterface $items) {
    $row_item_field = self::getRowItemsField($this->fieldDefinition);
    $all_items = [];
    foreach ($items->referencedEntities() as $row_delta => $row_entity) {
      $all_items[$row_delta]['row'] = [
        'target_id' => $row_entity->id(),
        'entity' => ['type' => [['target_id' => $row_entity->bundle()]]],
      ];

      /** @var \Drupal\paragraphs\ParagraphInterface $row_item */
      foreach ($row_entity->get($row_item_field->getName())
                 ->referencedEntities() as $item_delta => $row_item) {
        $all_items[$row_delta]['rowItems'][$item_delta] = [
          'target_id' => $row_item->id(),
          'entity' => ['type' => [['target_id' => $row_item->bundle()]]],
          'settings' => [
            'width' => $row_item->getBehaviorSetting('react', 'width'),
            'admin_title' => $row_item->getBehaviorSetting('react', 'label'),
          ],
        ];
      }

      $all_items[$row_delta]['rowItems'] = array_values($all_items[$row_delta]['rowItems']);
    }
    return $all_items;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    if(!empty($this->rowData)){
      return $this->rowData;
    }

    $react_data = json_decode(urldecode($values['container']['value']), TRUE);

    $return_data = [];

    // Nothing was added to the rows so there's nothing to do.
    if (empty($react_data['rowOrder'])) {
      return $return_data;
    }
    $items_field_name = self::getRowItemsField($this->fieldDefinition)
      ->getName();

    foreach ($react_data['rowOrder'] as $row_delta => $row_id) {
      $row_data = $react_data['rows'][$row_id];

      foreach ($row_data['itemsOrder'] as $item_id) {
        $item = $row_data['items'][$item_id];
        $row_data['entity'][$items_field_name][] = ['entity' => $this->getRowItemEntity($item['entity'], $item['width'], $item['admin_title'])];
      }

      $return_data[] = ['entity' => $this->getRowEntity($row_data['entity'])];
    }
    $this->rowData = $return_data;
    return $this->rowData;
  }

  protected function getRowEntity(array $field_data) {
    return $this->getEntity('paragraphs_row', $this->getRowBundle(), $field_data);
  }

  protected function getRowItemEntity(array $field_data, $width, $admin_label) {
    /** @var \Drupal\paragraphs\ParagraphInterface $row_item */
    $row_item = $this->getEntity('paragraph', $field_data['type'][0]['target_id'], $field_data);
    $row_item->getBehaviorSetting('react', 'width', $width);
    $row_item->getBehaviorSetting('react', 'label', $admin_label);
    return $row_item;
  }

  protected function getRowBundle() {
    $settings = $this->fieldDefinition->getSetting('handler_settings');
    return reset($settings['target_bundles']);
  }

  protected function getEntity($entity_type, $bundle, array $field_data) {
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
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $storage->create($entity_values);

    // TODO: Handle validation.
    $entity->validate();
    return $entity;
  }

}
