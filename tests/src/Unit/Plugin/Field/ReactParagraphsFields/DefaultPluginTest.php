<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\DefaultPlugin;

/**
 * Tests the React field data from the fallback default plugin.
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\DefaultPlugin
 */
class DefaultPluginTest extends ReactParagraphsFieldsTestBase {

  /**
   * {@inheritDoc}
   */
  public function setup(): void {
    parent::setUp();
    $this->plugin = DefaultPlugin::create($this->container, [], 'default', []);
    $this->fieldStorage->method('getType')->willReturn('foo_bar_baz');
  }

  /**
   * Test the default plugin.
   */
  public function testPlugin() {
    $data = $this->plugin->getFieldInfo([], $this->fieldConfig);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'foo_bar_baz',
    ];
    $this->assertEquals($expected, $data);
  }

}
