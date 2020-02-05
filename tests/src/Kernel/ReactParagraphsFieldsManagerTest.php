<?php

namespace Drupal\Tests\react_paragraphs\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Class ReactParagraphsFieldsManagerTest.
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\ReactParagraphsFieldsManager
 */
class ReactParagraphsFieldsManagerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['system', 'field', 'react_paragraphs'];

  /**
   * Test the plugin manager returns the plugins.
   */
  public function testPluginManager() {
    /** @var \Drupal\react_paragraphs\ReactParagraphsFieldsManager $plugin_manager */
    $plugin_manager = \Drupal::service('plugin.manager.react_paragraphs_fields');
    $this->assertCount(12, $plugin_manager->getDefinitions());
  }

}
