<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

/**
 * Class BooleanTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\Text
 */
class TextTest extends ReactParagraphsFieldsTestBase {

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
      'formatted' => FALSE,
      'text_type' => 'text',
      'max_length' => 0
    ];
    $this->assertEquals($expected, $data);
  }

  /**
   * Test the Email Fields.
   */
  public function testEmail() {
    $this->fieldConfig->method('getType')->willReturn('email');
    $data = $this->plugin->getFieldInfo([], $this->fieldConfig);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'foo_bar',
      'formatted' => FALSE,
      'text_type' => 'email',
      'max_length' => 0
    ];
    $this->assertEquals($expected, $data);
  }

  /**
   * Test the long text areas..
   */
  public function testTextArea() {
    $this->fieldConfig->method('getType')->willReturn('string_long');
    $data = $this->plugin->getFieldInfo([], $this->fieldConfig);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'foo_bar',
      'formatted' => FALSE,
      'text_type' => 'textarea',
      'max_length' => 0
    ];
    $this->assertEquals($expected, $data);
  }

}
