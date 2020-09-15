<?php

namespace Drupal\react_paragraphs_behaviors\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\react_paragraphs_behaviors\ReactBehaviorsPluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ReactBehaviorsDeriver.
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
    return new static($base_plugin_id, $container->get('plugin.manager.react_behaviors'));
  }

  public function __construct($base_plugin_id, ReactBehaviorsPluginManagerInterface $behaviors_manager) {
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
