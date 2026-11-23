<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\filter\FilterFormatInterface;

/**
 * Tests the React field data for formatted text fields.
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\Ckeditor
 */
class CkeditorTest extends ReactParagraphsFieldsTestBase {

  /**
   * {@inheritDoc}
   */

  /**
   * Test the field plugin.
   */
  public function testPlugin() {
    $this->fieldConfig->method('getThirdPartySettings')
      ->willReturn(['foo_bar' => 'foo_bar']);
    $data = $this->plugin->getFieldInfo([], $this->fieldConfig);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'foo_bar',
      'allowed_formats' => [
        'foo_bar' => 'Foo Bar',
      ],
      'summary' => FALSE,
    ];
    $this->assertEquals($expected, $data);
  }

  /**
   * {@inheritDoc}
   */
  protected function getFilterFormatRepository() {
    $filter = $this->createMock(FilterFormatInterface::class);
    $filter->method('label')->willReturn('Foo Bar');

    $filter_two = $this->createMock(FilterFormatInterface::class);
    $filter_two->method('label')->willReturn('Bar Foo');

    $repository = parent::getFilterFormatRepository();
    $repository->method('getFormatsForAccount')
      ->willReturn(['foo_bar' => $filter, 'bar_filter' => $filter_two]);
    return $repository;
  }

}
