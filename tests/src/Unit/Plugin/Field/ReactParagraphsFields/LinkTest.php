<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Routing\UrlGeneratorInterface;

/**
 * Class BooleanTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\Link
 */
class LinkTest extends ReactParagraphsFieldsTestBase {

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $url_generator = $this->createMock(UrlGeneratorInterface::class);
    $url_generator->method('generateFromRoute')->willReturn('/foo-bar');
    \Drupal::getContainer()->set('url_generator', $url_generator);
  }

  /**
   * Test the field plugin.
   */
  public function testPlugin() {
    $element = [
      'widget' => [
        [
          'uri' => [
            '#type' => 'entity_autocomplete',
            '#autocomplete_route_name' => 'foo_bar',
            '#autocomplete_route_parameters' => ['target_type' => 'node'],
          ],
        ],
      ],
    ];
    $this->fieldConfig->method('getSetting')->willReturn('Bar Foo');
    $data = $this->plugin->getFieldInfo($element, $this->fieldConfig);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'foo_bar',
      'autocomplete' => '/foo-bar',
      'target_type' => 'node',
      'title' => 'Bar Foo',
    ];
    $this->assertArrayEquals($expected, $data);
  }

}
