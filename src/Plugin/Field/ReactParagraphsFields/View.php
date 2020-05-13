<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Viewfield Plugin.
 *
 * @ReactParagraphsFields(
 *   id = "viewfield",
 *   field_types = {
 *     "viewfield"
 *   }
 * )
 */
class View extends ReactParagraphsFieldsBase {

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
    $info['views'] = [];
    $info['displays'] = [];
    $widget = $field_element['widget'][0];
    unset($widget['target_id']['#options']['_none']);

    $allowed_views_keys = array_keys($widget['target_id']['#options']);
    $allowed_views_values = array_values($widget['target_id']['#options']);
    $allowed_displays = $widget['display_id']['#options'];

    // TODO: Get these default values working.
    // $info['defaultValue']['views'] = $widget['target_id']['#default_value'];
    // $info['defaultValue']['displays'] = $widget['display_id']['#default_value'];

    foreach ($allowed_views_keys as $i => $v) {
      $info['views'][] = [
        'value' => $allowed_views_keys[$i],
        'label' => $allowed_views_values[$i],
      ];

      foreach ($allowed_displays as $display_name => $display_label) {
        $info['displays'][$allowed_views_keys[$i]][] = [
          'value' => $display_name,
          'label' => $display_label,
        ];
      }
    }

    return $info;
  }

}
