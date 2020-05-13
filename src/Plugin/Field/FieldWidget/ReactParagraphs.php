<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\ParagraphsTypeInterface;
use Drupal\paragraphs\Plugin\EntityReferenceSelection\ParagraphSelection;
use Drupal\react_paragraphs\Entity\ParagraphsRow;

/**
 * Plugin implementation of the 'react_paragraphs' widget.
 *
 * @FieldWidget(
 *   id = "react_paragraphs",
 *   label = @Translation("React Paragraphs"),
 *   field_types = {
 *     "react_paragraphs"
 *   },
 *   multiple_values = true
 * )
 */
class ReactParagraphs extends ReactParagraphsWidgetBase {

  /**
   * List of paragraph types.
   *
   * @var \Drupal\paragraphs\Entity\ParagraphsType[]
   */
  protected $paragraphTypes;

  /**
   * List of previously load paragraphs.
   *
   * @var \Drupal\paragraphs\ParagraphInterface[]
   */
  protected $paragraphs = [];

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $settings['items_per_row'] = 1;
    $settings['resizable'] = FALSE;
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['items_per_row'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of items per row'),
      '#min' => 1,
      '#max' => 6,
      '#default_value' => $this->getSetting('items_per_row'),
    ];
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
    $string_args = [
      '@resizable' => $this->getSetting('resizable') ? $this->t('Resizable') : '',
      '@count' => $this->getSetting('items_per_row'),
    ];

    return [
      $this->formatPlural($this->getSetting('items_per_row'), '@resizable @count item per row', '@resizable @count items per row', $string_args),
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
    $input_id = Html::getUniqueId($element_id . '-input');

    // Get all the attachments for any CKEditor fields that could exist.
    $attachments = $this->editorManager->getAttachments(array_keys(filter_formats($this->currentUser)));
    $attachments['library'][] = 'react_paragraphs/field_widget';

    // Set the javascript settings to be picked up by react.
    $attachments['drupalSettings']['reactParagraphs'][] = [
      'fieldId' => $element_id,
      'inputId' => $input_id,
      'tools' => $this->getTools($this->fieldDefinition),
      'items' => $this->getRowItems($items),
      'itemsPerRow' => $this->getSetting('items_per_row'),
      'resizableItems' => (bool) $this->getSetting('resizable'),
    ];

    // The hidden input with a empty container nearby for react to attach to.
    $element['container']['value'] = $element;
    $element['container']['value'] += [
      '#prefix' => '<div id="' . $element_id . '"></div>',
      '#type' => 'hidden',
      '#attached' => $attachments,
      '#attributes' => ['id' => $input_id],
    ];;

    $element['#attached']['library'][] = 'react_paragraphs/field_widget';
    return $element;
  }

  protected function getRowItems(FieldItemListInterface $items) {
    $all_items = [];
    foreach ($items->referencedEntities() as $row_delta => $row_entity) {
      $all_items[$row_delta]['row'] = [
        'target_id' => $row_entity->id(),
        'entity' => ['type' => [['target_id' => $row_entity->bundle()]]],
      ];

      foreach ($row_entity->get('su_row_items')->referencedEntities() as $item_delta => $row_item) {
        $all_items[$row_delta]['rowItems'][$item_delta] = [
          'target_id' => $row_item->id(),
          'entity' => ['type' => [['target_id' => $row_item->bundle()]]],
          'settings' => [
            'width' => 12,
            'admin_title' => 'Title',
          ],
        ];
      }

      $all_items[$row_delta]['rowItems'] = array_values($all_items[$row_delta]['rowItems']);
    }
    return $all_items;
  }

  /**
   * Get the available paragraph types that are allowed in this field.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   Field storage definition.
   *
   * @return array
   *   Keyed array of tool options with label and icon urls.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getTools(FieldDefinitionInterface $field_definition) {
    $return_bundles = [];
    $target_bundles = $field_definition->getSettings()['handler_settings']['target_bundles'];
    $row_bundle_id = reset($target_bundles);
    $row_item_field = $this->entityTypeManager->getStorage('field_config')
      ->load("paragraphs_row.$row_bundle_id.su_row_items");

    $handler = $this->selectionManager->getSelectionHandler($row_item_field);
    // Get a list of paragraph types that are allowed in the current field.
    if ($handler instanceof ParagraphSelection) {
      $field_bundles = $handler->getSortedAllowedTypes();
    }

    // Load the paragraph types to check for icons.
    $bundle_entities = $this->entityTypeManager->getStorage('paragraphs_type')
      ->loadMultiple(array_keys($field_bundles));

    /** @var \Drupal\paragraphs\ParagraphsTypeInterface $paragraph_type */
    foreach ($bundle_entities as $id => $paragraph_type) {
      $return_bundles[] = [
        'id' => $id,
        'label' => $paragraph_type->label(),
        'description' => $paragraph_type->getDescription(),
        'icon' => self::getParagraphTypeIcon($paragraph_type),
        'minWidth' => 1,
      ];
    }

    return $return_bundles;
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
        ->error('Unable to get paragraph icon for %type', ['%type' => $type]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $react_data = json_decode(urldecode($values['container']['value']), TRUE);
    $return_data = [];

    // Nothing was added to the rows so there's nothing to do.
    if (empty($react_data['rowOrder'])) {
      return $return_data;
    }

    // Loop through each row from react, then loop through the items in each
    // row, build the entity, and store the row/order data into the settings.
    foreach ($react_data['rowOrder'] as $row_number => $row_id) {

      $row_field_value = [];
      foreach ($react_data['rows'][$row_id]['itemsOrder'] as $item_number => $item_id) {
        $item = $react_data['rows'][$row_id]['items'][$item_id];

        // In case there was any incorrect data passed to the entity, we can
        // still save all the other paragraphs.
        try {
          $entity = $this->getEntity($item);
          $row_field_value[] = ['entity' => $entity];
        }
        catch (\Exception $e) {
          $this->messenger()
            ->addError($this->t('Unable to create the item %title in row %row. See logs for more information.', [
              '%title' => $item['admin_title'],
              '%row' => $row_number,
            ]));

          \Drupal::logger('react_paragraphs')
            ->error($this->t('Unable to create entity: %error. @data'), [
              '%error' => $e->getMessage(),
              '%data' => htmlSpecialChars(json_encode($item)),
            ]);
          continue;
        }
      }

      $row = $this->getRowEntity();
      $row->set('su_row_items', $row_field_value);
      $return_data[] = ['entity' => $row];
    }
    return $return_data;
  }

  /**
   * @return \Drupal\react_paragraphs\Entity\ParagraphsRowInterface
   */
  protected function getRowEntity() {
    return ParagraphsRow::create(['type' => 'basic_page_row', 'label' => 'Foo Bar']);
  }

  /**
   * Get the existing paragraph entity if it exists, or create a new one.
   *
   * @param array $item_data
   *   Entity field data.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   Paragraph entity.
   */
  protected function getEntity(array $item_data) {

    // The paragraph was already created/loaded. We can return that.
    if (isset($this->paragraphs[$item_data['id']])) {
      return $this->paragraphs[$item_data['id']];
    }

    // An existing paragraph was edited. Load that paragraph and set all the
    // field values to the data passed in.
    if (!empty($item_data['target_id'])) {
      $entity = Paragraph::load($item_data['target_id']);

      /** @var \Drupal\Core\Field\FieldDefinitionInterface $field_definition */
      foreach ($entity->getFieldDefinitions() as $field_definition) {
        if (isset($item_data['entity'][$field_definition->getName()]) && $field_definition instanceof FieldConfigInterface) {
          $entity->set($field_definition->getName(), $item_data['entity'][$field_definition->getName()]);
        }
      }
    }
    else {
      // Create a new paragraph entity. We don't need to save at this point.
      $entity = Paragraph::create($item_data['entity']);
    }

    $this->paragraphs[$item_data['id']] = $entity;
    return $this->paragraphs[$item_data['id']];
  }

}
