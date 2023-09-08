<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\views\ViewEntityInterface;

/**
 * Class BooleanTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\View
 */
class ViewTest extends ReactParagraphsFieldsTestBase {

  public function setup(): void {
    parent::setUp();
    $this->fieldConfig->method('getSetting')->willReturn([
      'block' => 'block',
      'master' => 0,
    ]);
  }

  /**
   * Test the field plugin.
   */
  public function testPlugin() {

    $field_element['widget'][0]['target_id']['#options'] = ['foo' => 'Foo'];
    $data = $this->plugin->getFieldInfo($field_element, $this->fieldConfig);

    $expected = [
      'label' => 'Foo Bar',
      'cardinality' => 1,
      'widget_type' => 'foo_bar',
      'help' => 'Description',
      'required' => TRUE,
      'weight' => 0,
      'displays' => [
        'foo' => [
          [
            'value' => 'block',
            'label' => 'Block',
          ],
        ],
      ],
      'views' => [
        [
          'value' => 'foo',
          'label' => 'Foo',
        ],
      ],
    ];

    $this->assertSame($expected, $data);
  }

  /**
   * Get View storage callback.
   */
  public function getStorageCallback() {
    $displays = [
      'master' => [
        'display_plugin' => 'master',
        'display_title' => 'Master',
        'position' => 1,
      ],
      'block' => [
        'display_plugin' => 'block',
        'display_title' => 'Block',
      ],
    ];
    $view = $this->createMock(ViewEntityInterface::class);
    $view->method('get')->willReturn($displays);
    $view->method('id')->willReturn('foo');
    $view->method('label')->willReturn('Foo');

    $view_storage = $this->createMock(EntityStorageInterface::class);
    $view_storage->method('loadMultiple')->willReturn([$view]);

    return $view_storage;
  }

}
