<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;

/**
 * Class BooleanTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\Telephone
 */
class TelephoneTest extends ReactParagraphsFieldsTestBase {

  /**
   * Test the field plugin.
   */
  public function testPlugin() {
    $field_config = $this->createMock(FieldConfigInterface::class);
    $field_config->method('getLabel')->willReturn('Foo Bar');
    $field_config->method('getDescription')->willReturn('Description');
    $field_config->method('isRequired')->willReturn(TRUE);

    $field_storage = $this->createMock(FieldStorageConfigInterface::class);
    $field_config->method('getFieldStorageDefinition')->willReturn($field_storage);
    $field_storage->method('getCardinality')->willReturn(1);

    $data = $this->plugin->getFieldInfo([], $field_config);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'foo_bar',
    ];
    $this->assertArrayEquals($expected, $data);
  }

}
