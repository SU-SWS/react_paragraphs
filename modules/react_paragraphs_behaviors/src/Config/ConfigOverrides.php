<?php

namespace Drupal\react_paragraphs_behaviors\Config;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;

/**
 * Class ConfigOverrides.
 *
 * @package Drupal\react_behaviors\Config
 */
class ConfigOverrides implements ConfigFactoryOverrideInterface {

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
   */
  protected function loadTypeOverrides(array $names, array &$overrides, $config_prefix) {
    foreach ($names as $config_name) {

      if (strpos($config_name, $config_prefix) === FALSE) {
        continue;
      }

      [, $entity_type, $bundle] = explode('.', $config_name);

      foreach (self::getBehaviorDefinitions() as $id => $definition) {
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
   *   Behavior plugin definition.
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
   * Need this function to be static without dependency injection to avoid
   * circular dependencies.
   *
   * @return array
   *   Keyed array of plugin definitions.
   */
  protected static function getBehaviorDefinitions() {
    return \Drupal::service('plugin.manager.react_behaviors')->getDefinitions();
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
