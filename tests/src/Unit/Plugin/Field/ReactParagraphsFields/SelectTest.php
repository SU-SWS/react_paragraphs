<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Tests the React field data for list (select) fields.
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
    $this->assertEquals($expected, $data);
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
    $this->assertEquals($expected, $data);
  }

  /**
   * Entity type manager get storage callback.
   *
   * @param string $type
   *   Entity type id.
   *
   * @return \PHPUnit\Framework\MockObject\MockObject
   *   Mocked webform storage.
   */
  public function getStorageCallback($type) {
    $webform_storage = $this->createMock(TestWebformEntityStorageInterface::class);
    $webform_storage->method('getOptions')->willReturn([]);
    return $webform_storage;
  }

}

/**
 * Webform storage interface with the getOptions() method for mocking.
 */
interface TestWebformEntityStorageInterface extends EntityStorageInterface {

  /**
   * Gets the webform options.
   */
  public function getOptions();

}
