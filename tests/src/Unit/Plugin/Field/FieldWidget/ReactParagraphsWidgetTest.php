<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\FieldWidget;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\paragraphs\ParagraphsTypeInterface;
use Drupal\react_paragraphs\Plugin\Field\FieldWidget\ReactParagraphs;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the React paragraphs widget's paragraph type icon handling.
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\FieldWidget\ReactParagraphs
 */
class ReactParagraphsWidgetTest extends UnitTestCase {

  /**
   * Sets up a container with a mocked logger factory.
   */
  public function setup(): void {
    parent::setUp();
    $container = new ContainerBuilder();

    $logger_channel = $this->createMock(LoggerChannelInterface::class);

    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $logger_factory->method('get')->willReturn($logger_channel);

    $container->set('logger.factory', $logger_factory);
    \Drupal::setContainer($container);
  }

  /**
   * Test when the exception is thrown, we still get null.
   */
  public function testException() {
    $paragraph_type = $this->createMock(ParagraphsTypeInterface::class);
    $paragraph_type->method('getIconUrl')->willThrowException(new \Exception('This broke'));

    $this->assertNull(TestReactParagraphsWidget::callGetParagraphTypeIcon($paragraph_type));
  }

}

/**
 * Test widget that exposes the protected icon method.
 */
class TestReactParagraphsWidget extends ReactParagraphs {

  /**
   * Calls the protected getParagraphTypeIcon() method.
   *
   * @param \Drupal\paragraphs\ParagraphsTypeInterface $type
   *   Paragraph type.
   *
   * @return string|null
   *   The icon URL, or NULL when it can't be found.
   */
  public static function callGetParagraphTypeIcon(ParagraphsTypeInterface $type) {
    return static::getParagraphTypeIcon($type);
  }

}
