<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\editor\Plugin\EditorManager;
use Drupal\field\FieldConfigInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Plugin\EntityReferenceSelection\ParagraphSelection;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
class ReactParagraphs extends WidgetBase implements ContainerFactoryPluginInterface {

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
   * Current account object.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * List of paragraph types.
   *
   * @var \Drupal\paragraphs\Entity\ParagraphsType[]
   */
  protected $paragraphTypes;

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
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, SelectionPluginManagerInterface $selection_manager, EntityTypeManagerInterface $entity_type_manager, EditorManager $editor_manager, AccountProxyInterface $current_user) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->selectionManager = $selection_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->editorManager = $editor_manager;
    $this->currentUser = $current_user;
  }

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
    ];

    $item_values = array_filter($items->getValue());
    foreach ($item_values as $delta => &$item) {

      $item['settings'] = json_decode($item['settings'], TRUE);
      if (is_array($item['settings'])) {
        continue;
      }

      // In the circumstance that the items don't have settings data, just put
      // them into a single column.
      $admin_title = $this->t('Item @delta', ['@delta' => $delta])->render();
      $item['settings'] = [
        'row' => $delta,
        'index' => 0,
        'width' => 12,
        'admin_title' => $this->getItemLabel($item['target_id']),
      ];
    }

    $element_id = Html::getUniqueId('react-' . $this->fieldDefinition->getName());
    $input_id = Html::getUniqueId($element_id . '-input');

    $attachments = $this->editorManager->getAttachments(array_keys(filter_formats($this->currentUser)));
    $attachments['library'][] = 'react_paragraphs/field_widget';
    $attachments['drupalSettings']['reactParagraphs'][] = [
      'fieldId' => $element_id,
      'inputId' => $input_id,
      'tools' => $this->getTools($this->fieldDefinition),
      'items' => $item_values,
      'itemsPerRow' => $this->getSetting('items_per_row'),
      'resizableItems' => (bool) $this->getSetting('resizable'),
    ];

    $element['container']['value'] = $element;
    $element['container']['value'] += [
      '#prefix' => '<div id="' . $element_id . '"></div>',
      '#type' => 'hidden',
      '#attached' => $attachments,
      '#attributes' => ['id' => $input_id],
    ];

    $element['#attached']['library'][] = 'react_paragraphs/field_widget';
    return $element;
  }

  /**
   * Get the paragraph type bundle name from the entity id to use as the label.
   *
   * @param int $entity_id
   *   Paragraph entity id.
   *
   * @return string
   *   Bundle name or the entity id.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getItemLabel($entity_id) {
    if (empty($this->paragraphTypes)) {
      $this->paragraphTypes = $this->entityTypeManager->getStorage('paragraphs_type')->loadMultiple();
    }
    if ($paragraph = $this->entityTypeManager->getStorage('paragraph')->load($entity_id)) {
      return $this->paragraphTypes[$paragraph->bundle()]->label();
    }
    return (string) $entity_id;
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
    $handler = $this->selectionManager->getSelectionHandler($field_definition ?: $this->fieldDefinition);
    if ($handler instanceof ParagraphSelection) {
      $return_bundles = $handler->getSortedAllowedTypes();
    }

    $bundle_entities = $this->entityTypeManager->getStorage('paragraphs_type')
      ->loadMultiple(array_keys($return_bundles));

    /** @var \Drupal\paragraphs\ParagraphsTypeInterface $paragraph_type */
    foreach ($bundle_entities as $id => $paragraph_type) {
      $return_bundles[$id]['icon'] = NULL;

      if ($icon_uuid = $paragraph_type->get('icon_uuid')) {
        $file = $this->entityTypeManager->getStorage('file')
          ->loadByProperties(['uuid' => $icon_uuid]);
        if (!empty($file)) {
          /** @var \Drupal\file\Entity\File $file */
          $file = is_array($file) ? reset($file) : $file;
          $return_bundles[$id]['icon'] = file_create_url($file->getFileUri());
        }
      }
    }

    return $return_bundles;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $react_data = json_decode(urldecode($values['container']['value']), TRUE);

    $return_data = [];

    if (!isset($react_data['rowOrder'])) {
      return $return_data;
    }

    foreach ($react_data['rowOrder'] as $row_number => $row_id) {
      foreach ($react_data['rows'][$row_id]['itemsOrder'] as $item_number => $item_id) {
        $item = $react_data['rows'][$row_id]['items'][$item_id];

        try {
          $entity = $this->getEntity($item);
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

        $settings = [
          'row' => $row_number,
          'index' => $item_number,
          'width' => $item['width'],
          'admin_title' => $item['admin_title'],
        ];

        $return_data[] = [
          'entity' => $entity,
          'target_id' => $entity->id(),
          'target_revision_id' => $entity->getRevisionId(),
          'settings' => json_encode($settings),
        ];
      }
    }
    return $return_data;
  }

  /**
   * Get the existing paragraph entity if it exists, or create a new one.
   *
   * @param array $item_data
   *   Entity field data.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   Paragraph entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getEntity(array $item_data) {
    if (isset($this->paragraphIds[$item_data['id']])) {
      return $this->paragraphIds[$item_data['id']];
    }

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
      $entity = Paragraph::create($item_data['entity']);
      $entity->save();
    }

    $this->paragraphIds[$item_data['id']] = $entity;
    return $this->paragraphIds[$item_data['id']];
  }

}
