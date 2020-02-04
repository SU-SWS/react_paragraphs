<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\FieldWidget;

use Drupal\react_paragraphs\Plugin\Field\FieldWidget\ReactParagraphs;
use Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\ReactParagraphsFieldTestBase;

/**
 * Class ReactParagraphsTest
 *
 * @group react_paragraphs.
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\FieldWidget\ReactParagraphs
 */
class ReactParagraphsTest extends ReactParagraphsFieldTestBase {

  /**
   * Test the widget plugin.
   */
  public function testWidget() {
    $settings = ReactParagraphs::defaultSettings();
    $this->assertEquals(1, $settings['items_per_row']);
    $this->assertFalse($settings['resizable']);
  }

}
