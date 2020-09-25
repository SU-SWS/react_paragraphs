<?php

namespace Drupal\react_paragraphs_behaviors;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;

/**
 * Provides the default react_behaviors manager.
 */
class ReactBehaviorsPluginManager extends DefaultPluginManager implements ReactBehaviorsPluginManagerInterface {

  /**
   * Provides default values for all react_behaviors plugins.
   *
   * @var array
   */
  protected $defaults = [
    'id' => '',
    'label' => '',
  ];

  /**
   * Theme handler service.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * Constructs a new ReactBehaviorsPluginManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   */
  public function __construct(ModuleHandlerInterface $module_handler, ThemeHandlerInterface $theme_handler, CacheBackendInterface $cache_backend) {
    $this->moduleHandler = $module_handler;
    $this->themeHandler = $theme_handler;
    $this->setCacheBackend($cache_backend, 'react_behaviors', ['react_behaviors']);
  }

  /**
   * {@inheritdoc}
   */
  protected function providerExists($provider) {
    return $this->moduleHandler->moduleExists($provider) || $this->themeHandler->themeExists($provider);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!isset($this->discovery)) {
      $this->discovery = new YamlDiscovery('react_behaviors', $this->moduleHandler->getModuleDirectories() + $this->themeHandler->getThemeDirectories());
      $this->discovery->addTranslatableProperty('label', 'label_context');
      $this->discovery = new ContainerDerivativeDiscoveryDecorator($this->discovery);
    }
    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);

    if (empty($definition['config'])) {
      throw new PluginException(sprintf('Plugin property (%s) definition "config" is required.', $plugin_id));
    }
    if (empty($definition['label'])) {
      throw new PluginException(sprintf('Plugin property (%s) definition "label" is required.', $plugin_id));
    }
  }

}
