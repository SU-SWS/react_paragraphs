<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;

/**
 * Class BooleanTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\Datetime
 */
class DatetimeTest extends ReactParagraphsFieldsTestBase {

  /**
   * Test the field plugin.
   */
  public function testPlugin() {
    $this->fieldStorage->method('getSetting')->willReturn('date_time');
    $data = $this->plugin->getFieldInfo([], $this->fieldConfig);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'foo_bar',
      'type' => 'date_time',
    ];
    $this->assertEquals($expected, $data);
  }

}
