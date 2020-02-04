<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\FieldType;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\react_paragraphs\Plugin\Field\FieldType\ReactParagraphs;
use Drupal\Tests\UnitTestCase;

/**
 * Class ReactParagraphsTest
 *
 * @group react_paragraphs.
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\FieldType\ReactParagraphs
 */
class ReactParagraphsTest extends UnitTestCase {

  protected $container;

  protected function setUp() {
    parent::setUp();
    $this->container = new ContainerBuilder();
    $this->container->set('module_handler', $this->createMock(ModuleHandlerInterface::class));
    $this->container->set('entity_type.manager', $this->createMock(EntityTypeManagerInterface::class));
    \Drupal::setContainer($this->container);
  }

  public function testFieldType() {
    $expected = ['target_type' => 'paragraph'];
    $this->assertArrayEquals($expected, ReactParagraphs::defaultStorageSettings());
  }

}
