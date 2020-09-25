<?php

namespace Drupal\react_paragraphs_behaviors\Config;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\react_paragraphs_behaviors\ReactBehaviorsPluginManagerInterface;

/**
 * Class ConfigOverrides
 *
 * @package Drupal\react_behaviors\Config
 */
class ConfigOverrides implements ConfigFactoryOverrideInterface {

  /**
   * Behavior yaml plugin manager for behavior derivatives.
   *
   * @var \Drupal\react_paragraphs_behaviors\ReactBehaviorsPluginManagerInterface
   */
  protected $pluginManager;

  /**
   * Keyed array of behavior derivative definitions.
   *
   * @var array
   */
  protected $behaviors = [];

  /**
   * ConfigOverrides constructor.
   *
   * @param \Drupal\react_paragraphs_behaviors\ReactBehaviorsPluginManagerInterface $plugin_manager
   */
  public function __construct(ReactBehaviorsPluginManagerInterface $plugin_manager) {
    $this->pluginManager = $plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function loadOverrides($names) {
    $overrides = [];
    foreach ($names as $config_name) {
      $config_prefix = 'paragraphs.paragraphs_type.';

      if (strpos($config_name, $config_prefix) === FALSE) {
        continue;
      }
      $paragraph_type = substr($config_name, strlen($config_prefix));

      foreach ($this->getBehaviorDefinitions() as $id => $definition) {
        if (empty($definition['paragraphs']) || in_array($paragraph_type, $definition['paragraphs'])) {
          $overrides[$config_name]['behavior_plugins']["react_paragraphs:$id"]['enabled'] = TRUE;
        }
      }
    }
    return $overrides;
  }

  /**
   * Get the behavior derivative definitions.
   *
   * @return array
   *   Keyed array of plugin definitions.
   */
  protected function getBehaviorDefinitions() {
    if ($this->behaviors) {
      return $this->behaviors;
    }

    $this->behaviors = $this->pluginManager->getDefinitions();
    return $this->behaviors;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheSuffix() {
    return 'ReactConfigOverrider';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore Nothing to test.
   */
  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

}
