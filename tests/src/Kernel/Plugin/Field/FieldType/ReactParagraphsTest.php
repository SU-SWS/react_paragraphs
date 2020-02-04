<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\FieldType;

use Drupal\react_paragraphs\Plugin\Field\FieldType\ReactParagraphs;
use Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\ReactParagraphsFieldTestBase;

/**
 * Class ReactParagraphsTest
 *
 * @group react_paragraphs.
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\FieldType\ReactParagraphs
 */
class ReactParagraphsTest extends ReactParagraphsFieldTestBase {

  /**
   * Test the field type methods.
   */
  public function testFieldType() {
    $settings = ReactParagraphs::defaultStorageSettings();
    $this->assertEqual($settings['target_type'], 'paragraph');
    $definitions = ReactParagraphs::propertyDefinitions($this->fieldStorage);
    $this->assertArrayHasKey('settings', $definitions);
    $schema = ReactParagraphs::schema($this->fieldStorage);
    $this->assertArrayHasKey('settings', $schema['columns']);
  }

}
