<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Class BooleanTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\Select
 */
class SelectTest extends ReactParagraphsFieldsTestBase {

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
      'column_key' => 'value',
      'options' => NULL,
      'default_value' => NULL,
    ];
    $this->assertArrayEquals($expected, $data);
  }

  /**
   * Test webform select field.
   */
  public function testWebforms() {
    $this->fieldConfig->method('getType')->willReturn('webform');
    $field_element['widget']['#default_value'] = ['foo'];
    $data = $this->plugin->getFieldInfo($field_element, $this->fieldConfig);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'foo_bar',
      'column_key' => 'target_id',
      'options' => [],
      'default_value' => ['foo'],
    ];
    $this->assertArrayEquals($expected, $data);
  }

  public function getStorageCallback() {
    $webform_storage = $this->createMock(TestWebformEntityStorageInterface::class);
    $webform_storage->method('getOptions')->willReturn([]);
    return $webform_storage;
  }

}

interface TestWebformEntityStorageInterface extends EntityStorageInterface {

  public function getOptions();

}
