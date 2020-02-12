<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

/**
 * Class BooleanTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\Number
 */
class NumberTest extends ReactParagraphsFieldsTestBase {

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
      'max' => NULL,
      'min' => NULL,
      'precision' => NULL,
      'scale' => NULL,
    ];
    $this->assertArrayEquals($expected, $data);
  }

}
