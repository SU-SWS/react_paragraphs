<?php

namespace Drupal\react_paragraphs_behaviors\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\react_paragraphs_behaviors\ReactBehaviorsPluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derives one paragraphs behavior plugin per React behavior definition.
 *
 * @package Drupal\react_paragraphs_behaviors\Plugin\Derivative
 */
class ReactBehaviorsDeriver extends DeriverBase implements ContainerDeriverInterface {

  /**
   * Plugin manager service.
   *
   * @var \Drupal\react_paragraphs_behaviors\ReactBehaviorsPluginManagerInterface
   */
  protected $behaviorsManager;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static($container->get('plugin.manager.react_behaviors'));
  }

  /**
   * ReactBehaviorsDeriver constructor.
   *
   * @param \Drupal\react_paragraphs_behaviors\ReactBehaviorsPluginManagerInterface $behaviors_manager
   *   Behavior plugin manager service.
   */
  final public function __construct(ReactBehaviorsPluginManagerInterface $behaviors_manager) {
    $this->behaviorsManager = $behaviors_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach ($this->behaviorsManager->getDefinitions() as $id => $definition) {
      $this->derivatives[$id] = $base_plugin_definition;
      $this->derivatives[$id]['label'] = $definition['label'] ?? $id;
      $this->derivatives[$id]['description'] = $definition['description'] ?? NULL;
      $this->derivatives[$id]['config'] = $definition['config'] ?? [];
      $this->derivatives[$id]['bundles'] = $definition['bundles'] ?? [];
    }
    return $this->derivatives;
  }

}
