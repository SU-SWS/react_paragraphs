<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\filter\FilterFormatInterface;
use Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\Ckeditor;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
  public function setup(): void {
    parent::setUp();
    $this->plugin = TestCkeditor::create($this->container, [], 'foo_bar', []);
  }

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
   * Entity type manager get storage callback.
   *
   * @return \PHPUnit\Framework\MockObject\MockObject
   *   Mocked object.
   */
  public function getStorageCallback($type) {
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
 * Test plugin that loads filter formats from the mocked entity storage.
 */
class TestCkeditor extends Ckeditor {

  /**
   * Filter format entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $filterFormatStorage;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->filterFormatStorage = $container->get('entity_type.manager')
      ->getStorage('filter_format');
    return $instance;
  }

  /**
   * {@inheritDoc}
   */
  protected function getFilterFormats() {
    return $this->filterFormatStorage->loadMultiple();
  }

}
