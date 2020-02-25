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
    $info['displays'] = [];
    $info['views'] = [];

    $view_storage = $this->entityTypeManager->getStorage('view');
    $view_options = $field_element['widget'][0]['target_id']['#options'];

    /** @var \Drupal\views\ViewEntityInterface $view */
    foreach ($view_storage->loadMultiple(array_keys($view_options)) as $view) {
      $displays = $view->get('display');
      $valid_displays = FALSE;
      foreach ($displays as $display_id => $display) {
        if ($display['display_plugin'] != 'block') {
          continue;
        }

        $valid_displays = TRUE;
        $info['displays'][$view->id()][] = [
          'value' => $display_id,
          'label' => $display['display_title'],
        ];
      }

      if ($valid_displays) {
        $info['views'][] = [
          'value' => $view->id(),
          'label' => $view->label(),
        ];
      }
    }

    return $info;
  }

}
