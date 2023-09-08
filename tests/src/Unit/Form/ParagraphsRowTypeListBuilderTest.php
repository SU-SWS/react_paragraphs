<?php

namespace Drupal\Tests\react_paragraphs\Unit\Form;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\react_paragraphs\ParagraphsRowTypeListBuilder;
use Drupal\Tests\UnitTestCase;

/**
 * Class ParagraphsRowTypeListBuilderTest.
 *
 * @group react_paragraphs
 */
class ParagraphsRowTypeListBuilderTest extends UnitTestCase {

  /**
   * List builder object.
   *
   * @var \Drupal\react_paragraphs\ParagraphsRowTypeListBuilder
   */
  protected $builder;

  /**
   * {@inheritDoc}
   */
  public function setup(): void {
    parent::setUp();
    $container = new ContainerBuilder();
    $entity_storage = $this->createMock(EntityStorageInterface::class);

    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_type_manager->method('getStorage')->willReturn($entity_storage);

    $module_handler = $this->createMock(ModuleHandlerInterface::class);
    $module_handler->method('invokeAll')->willReturn([]);

    $container->set('string_translation', $this->getStringTranslationStub());
    $container->set('entity_type.manager', $entity_type_manager);
    $container->set('module_handler', $module_handler);

    \Drupal::setContainer($container);

    $entity_type = $this->createMock(EntityTypeInterface::class);
    $this->builder = ParagraphsRowTypeListBuilder::createInstance(\Drupal::getContainer(), $entity_type);
  }

  public function testListBuilder() {
    $this->assertCount(3, $this->builder->buildHeader());

    $entity = $this->createMock(EntityInterface::class);
    $entity->method('label')->willReturn('Foo');
    $entity->method('id')->willReturn(1);

    $this->assertCount(3, $this->builder->buildRow($entity));
  }

}
