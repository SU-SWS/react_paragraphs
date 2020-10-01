<?php

namespace Drupal\Tests\react_paragraphs_behaviors\Kernel;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class ReactBehaviorsPluginManagerTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs_behaviors\ReactBehaviorsPluginManager
 */
class ReactBehaviorsPluginManagerTest extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'system',
    'react_paragraphs_behaviors',
    'test_react_paragraphs_behaviors',
  ];

  /**
   * With the test module enabled, 2 plugins should be discovered.
   */
  public function testDiscovery() {
    $definitions = \Drupal::service('plugin.manager.react_behaviors')
      ->getDefinitions();
    $this->assertCount(3, $definitions);
  }

  /**
   * The plugin will throw an error if it doesn't have any config.
   */
  public function testMissingConfig() {
    $definition = ['config' => []];
    $this->expectException(PluginException::class);
    \Drupal::service('plugin.manager.react_behaviors')
      ->processDefinition($definition, 'foo_bar');
  }

  /**
   * The plugin will throw an error if it doesn't have a label.
   */
  public function testMissingLabel() {
    $definition = ['config' => ['foo' => []]];
    $this->expectException(PluginException::class);
    \Drupal::service('plugin.manager.react_behaviors')
      ->processDefinition($definition, 'foo_bar');
  }

}
