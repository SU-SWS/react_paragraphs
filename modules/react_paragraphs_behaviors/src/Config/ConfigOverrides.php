<?php

namespace Drupal\react_paragraphs_behaviors\Config;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\react_paragraphs_behaviors\ReactBehaviorsPluginManagerInterface;

/**
 * Class ConfigOverrides.
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
   *   Behavior plugin manager service.
   */
  public function __construct(ReactBehaviorsPluginManagerInterface $plugin_manager) {
    $this->pluginManager = $plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function loadOverrides($names) {
    $overrides = [];
    $this->loadTypeOverrides($names, $overrides, 'paragraphs.paragraphs_type.', 'paragraphs');
    $this->loadTypeOverrides($names, $overrides, 'react_paragraphs.paragraphs_row_type.', 'rows');
    return $overrides;
  }

  /**
   * Load the config overrides for the given config entity type.
   *
   * @param array $names
   *   Array of config names.
   * @param array $overrides
   *   Keyed array of config overrides.
   * @param $config_prefix
   *   Config prefix we are overriding.
   * @param $definition_key
   *   Behavior plugin key that indicates which ones it's enabled on.
   */
  protected function loadTypeOverrides(array $names, array &$overrides, $config_prefix, $definition_key) {
    foreach ($names as $config_name) {

      if (strpos($config_name, $config_prefix) === FALSE) {
        continue;
      }
      $paragraph_type = substr($config_name, strlen($config_prefix));

      foreach ($this->getBehaviorDefinitions() as $id => $definition) {
        if (!empty($definition[$definition_key]) && in_array($paragraph_type, $definition[$definition_key])) {
          $overrides[$config_name]['behavior_plugins']["react_paragraphs:$id"]['enabled'] = TRUE;
        }
      }
    }
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
