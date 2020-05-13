<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
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
      'target_type' => 'paragraphs_row',
    ];
    return $settings + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::fieldSettingsForm($form, $form_state);
    $form['handler']['handler_settings']['target_bundles']['#default_value'] = reset($form['handler']['handler_settings']['target_bundles']['#default_value']);
    $form['handler']['handler_settings']['target_bundles']['#type'] = 'radios';
    array_unshift($form['handler']['handler_settings']['target_bundles']['#element_validate'], [
      $this,
      'elementValidateFilter',
    ]);
    return $form;
  }

  public function elementValidateFilter(&$element, FormStateInterface $form_state) {
    $value = $form_state->getValue($element['#parents']);
    $element['#value'] = [$value];
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = parent::storageSettingsForm($form, $form_state, $has_data);
    $element['target_type']['#options'] = [
      'paragraphs_row' => $element['target_type']['#options']['paragraphs_row'],
    ];
    return $element;
  }

}
