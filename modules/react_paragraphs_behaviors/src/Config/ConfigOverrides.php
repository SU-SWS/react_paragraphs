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
    $this->loadTypeOverrides($names, $overrides, 'paragraphs.paragraphs_type.');
    $this->loadTypeOverrides($names, $overrides, 'react_paragraphs.paragraphs_row_type.');
    return $overrides;
  }

  /**
   * Load the config overrides for the given config entity type.
   *
   * @param array $names
   *   Array of config names.
   * @param array $overrides
   *   Keyed array of config overrides.
   * @param string $config_prefix
   *   Config prefix we are overriding.
   * @param string $definition_key
   *   Behavior plugin key that indicates which ones it's enabled on.
   */
  protected function loadTypeOverrides(array $names, array &$overrides, $config_prefix) {
    foreach ($names as $config_name) {

      if (strpos($config_name, $config_prefix) === FALSE) {
        continue;
      }

      [, $entity_type, $bundle] = explode('.', $config_name);

      foreach ($this->getBehaviorDefinitions() as $id => $definition) {
        if ($this->isBehaviorApplicable($definition, $entity_type, $bundle)) {
          $overrides[$config_name]['behavior_plugins']["react_paragraphs:$id"]['enabled'] = TRUE;
        }
      }
    }
  }

  /**
   * Is the behavior applicable to the give bundle and entity type.
   *
   * @param array $plugin_definition
   *   Behavior plugin definition
   * @param string $entity_type
   *   Entity type name.
   * @param string $bundle
   *   Entity bundle.
   *
   * @return bool
   *   If the given behavior plugin definition applies to the entity bundle.
   */
  protected function isBehaviorApplicable(array $plugin_definition, $entity_type, $bundle) {
    if (empty($plugin_definition['bundles'])) {
      return FALSE;
    }

    foreach ($plugin_definition['bundles'] as $plugin_limit) {
      [$entity_type_limit, $bundle_limit] = explode('|', $plugin_limit);

      if (
        ($entity_type_limit == $entity_type || $entity_type_limit == '*') &&
        ($bundle_limit == $bundle || $bundle_limit == '*')
      ) {
        return TRUE;
      }
    }
    return FALSE;
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
