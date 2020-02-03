<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\Ckeditor;

/**
 * Class BooleanTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\Ckeditor
 */
class CkeditorTest extends ReactParagraphsFieldsTestBase {

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->plugin = TestCkeditor::create($this->container, [], 'foo_bar', []);
  }

  /**
   * Test the field plugin.
   */
  public function testPlugin() {
    $data = $this->plugin->getFieldInfo([], $this->fieldConfig);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'foo_bar',
      'allowed_formats' => [],
      'summary' => FALSE,
    ];
    $this->assertArrayEquals($expected, $data);
  }

}

/**
 * {@inheritDoc}
 */
class TestCkeditor extends Ckeditor {

  /**
   * {@inheritDoc}
   */
  protected function getFilterFormats() {
    return [];
  }

}
