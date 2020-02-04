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

  public function preSave() {
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

}
