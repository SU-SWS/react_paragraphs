<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\entity_reference_revisions\Plugin\Field\FieldType\EntityReferenceRevisionsItem;

/**
 * Plugin implementation of the 'react_paragraphs' field type.
 *
 * @FieldType(
 *   id = "react_paragraphs",
 *   label = @Translation("React Paragraphs"),
 *   description = @Translation("My Field Type"),
 *   category = @Translation("Reference revisions"),
 *   default_widget = "react_paragraphs",
 *   default_formatter = "react_paragraphs",
 *   cardinality = -1,
 *   list_class = "\Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList"
 * )
 */
class ReactParagraphs extends EntityReferenceRevisionsItem {

  /**
   * Flag when the field data is being saved.
   *
   * @var bool
   */
  protected $duringSave = FALSE;

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    $settings = [
      'target_type' => 'paragraph',
    ];
    return $settings + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::fieldSettingsForm($form, $form_state);
    $settings = $this->getSettings();
    foreach ($form['handler']['handler_settings']['target_bundles']['#options'] as $key => $label) {
      $form['handler']['handler_settings']['widths'][$key] = [
        '#type' => 'details',
        '#title' => $this->t('%label settings', ['%label' => $label]),
      ];
      $form['handler']['handler_settings']['widths'][$key]['min'] = [
        '#type' => 'select',
        '#title' => $this->t('Minimum Width for %label', ['%label' => $label]),
        '#description' => $this->t('Set the minimum required columns out of 12 that this paragraph needs to function correctly.'),
        '#default_value' => $settings['handler_settings']['widths'][$key]['min'] ?? 1,
        '#options' => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 6 => 6, 12 => 12],
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function fieldSettingsFormValidate(array $form, FormStateInterface $form_state) {
    parent::fieldSettingsFormValidate($form, $form_state);

    $negate = $form_state->getValue(['settings', 'handler_settings', 'negate']);
    $bundles = $form_state->getValue(['settings', 'handler_settings', 'target_bundles_drag_drop']);

    // Find the paragraph bundles that are allowed in the widget.
    $enabled_bundles = array_filter($bundles, function ($bundle_settings) use ($negate) {
      return (!$bundle_settings['enabled'] && $negate) || ($bundle_settings['enabled'] && !$negate);
    });

    // Clean up the values if the bundle is not customized to with some widths.
    foreach ($form_state->getValue(['settings', 'handler_settings', 'widths']) as $bundle => $bundle_settings) {
      if (!array_key_exists($bundle, $enabled_bundles) || $bundle_settings['min'] == 1) {
        $form_state->unsetValue(['settings', 'handler_settings', 'widths', $bundle]);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = parent::storageSettingsForm($form, $form_state, $has_data);
    $element['target_type']['#options'] = [
      'paragraph' => $element['target_type']['#options']['paragraph'],
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);
    $properties['settings'] = DataDefinition::create('string')
      ->setLabel(t('Settings'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);
    $schema['columns']['settings'] = [
      'description' => 'Settings for the item.',
      'type' => 'blob',
      'size' => 'normal',
    ];
    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave() {
    $this->duringSave = TRUE;
    $this->writePropertyValue('settings', json_encode($this->settings));

    try {
      parent::preSave();
    }
    catch (\Exception $e) {
      \Drupal::messenger()
        ->addError($this->t('Unable to save one or more items: %message', ['%message' => $e->getMessage()]));
      \Drupal::logger('react_paragraphs')
        ->error($this->t('Unable to create entity: %message'), ['%message' => $e->getMessage()]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onChange($property_name, $notify = TRUE) {
    parent::onChange($property_name, $notify);
    if (!$this->duringSave && !is_array($this->settings)) {
      $settings = json_decode($this->settings, TRUE) ?: [];
      $this->writePropertyValue('settings', $settings);
    }
    $this->duringSave = FALSE;
  }

}
