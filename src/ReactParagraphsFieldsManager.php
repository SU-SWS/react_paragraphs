<?php

namespace Drupal\react_paragraphs;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides an ReactParagraphsFields plugin manager.
 *
 * @see \Drupal\Core\Archiver\Annotation\Archiver
 * @see \Drupal\Core\Archiver\ArchiverInterface
 * @see plugin_api
 */
class ReactParagraphsFieldsManager extends DefaultPluginManager {

  /**
   * Constructs a ReactParagraphsFieldsManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/Field/ReactParagraphsFields',
      $namespaces,
      $module_handler,
      'Drupal\react_paragraphs\ReactParagraphsFieldsInterface',
      'Drupal\react_paragraphs\Annotation\ReactParagraphsFields'
    );
    $this->alterInfo('react_paragraphs_fields_info');
    $this->setCacheBackend($cache_backend, 'react_paragraphs_fields_info_plugins');
  }

}
