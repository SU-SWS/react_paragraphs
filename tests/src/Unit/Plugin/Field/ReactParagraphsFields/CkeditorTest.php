<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\filter\FilterFormatInterface;
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
  protected function setUp(): void {
    parent::setUp();
    $this->plugin = TestCkeditor::create($this->container, [], 'foo_bar', []);
  }

  /**
   * Test the field plugin.
   */
  public function testPlugin() {
    $this->fieldConfig->method('getThirdPartySettings')
      ->willReturn(['foo_bar' => 'foo_bar',]);
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
    $this->assertArrayEquals($expected, $data);
  }

  /**
   * Entity type manager get storage callback.
   *
   * @return \PHPUnit\Framework\MockObject\MockObject
   *   Mocked object.
   */
  public function getStorageCallback() {
    $filter = $this->createMock(FilterFormatInterface::class);
    $filter->method('label')->willReturn('Foo Bar');

    $filter_two = $this->createMock(FilterFormatInterface::class);
    $filter_two->method('label')->willReturn('Bar Foo');

    $filter_storage = $this->createMock(EntityStorageInterface::class);
    $filter_storage->method('loadMultiple')
      ->willReturn(['foo_bar' => $filter, 'bar_filter' => $filter_two]);
    return $filter_storage;
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
    return \Drupal::entityTypeManager()
      ->getStorage('filter_format')
      ->loadMultiple();
  }

}
