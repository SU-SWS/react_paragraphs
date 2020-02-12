<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\rest\resource;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\Plugin\rest\resource\ReactParagraphsResource;
use Drupal\react_paragraphs\ReactParagraphsFieldsInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsManager;
use Drupal\Tests\UnitTestCase;

/**
 * Class ReactParagraphsResourceTest.
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\rest\resource\ReactParagraphsResource
 */
class ReactParagraphsResourceTest extends UnitTestCase {

  /**
   * @var \Drupal\react_paragraphs\Plugin\rest\resource\ReactParagraphsResource
   */
  protected $resource;

  protected $entityAccess = TRUE;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $logger_channel = $this->createMock(LoggerChannelInterface::class);

    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $logger_factory->method('get')->willReturn($logger_channel);

    $entity_definition = $this->createMock(EntityTypeInterface::class);
    $entity_definition->method('getKey')->willReturn('bundle');

    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_type_manager->method('getDefinition')->willReturn($entity_definition);
    $entity_type_manager->method('getStorage')->will($this->returnCallback([$this, 'getEntityStorageCallback']));

    $form = [
      'field_foo' => [],
      'field_bar' => ['widget' => []],
      'field_baz' => ['widget' => []],
    ];
    $form_builder = $this->createMock(EntityFormBuilderInterface::class);
    $form_builder->method('getForm')
      ->willReturn($form);

    $plugin = $this->createMock(ReactParagraphsFieldsInterface::class);
    $plugin->method('getFieldInfo')->willReturn(['foo' => 'bar']);

    $plugin_manager = $this->createMock(ReactParagraphsFieldsManager::class);
    $plugin_manager->method('getDefinitions')->willReturn([
      'foo' => ['id' => 'foo', 'field_types' => ['text']],
    ]);
    $plugin_manager->method('createInstance')->willReturn($plugin);

    $container = new ContainerBuilder();
    $container->setParameter('serializer.formats', []);
    $container->set('logger.factory', $logger_factory);
    $container->set('entity_type.manager', $entity_type_manager);
    $container->set('entity.form_builder', $form_builder);
    $container->set('plugin.manager.react_paragraphs_fields', $plugin_manager);

    $this->resource = ReactParagraphsResource::create($container, [], '', []);
  }

  /**
   * Response should be 401 if user doesn't have access to the entity.
   */
  public function testAccessDenied() {
    $this->entityAccess = FALSE;
    $response = $this->resource->get('paragraph', 'card');
    $this->assertEquals(401, $response->getStatusCode());
  }

  /**
   * Test the resource get request.
   */
  public function testGetRequest() {
    $this->assertArrayEquals([], $this->resource->permissions());
    $this->resource->get('paragraph', 'card');
  }

  /**
   * The entity type manager getStorage callback.
   *
   * @param string $type
   *   Entity type id.
   *
   * @return \PHPUnit\Framework\MockObject\MockObject
   *   Mocked storage.
   */
  public function getEntityStorageCallback($type) {
    $entity_storage = $this->createMock(EntityStorageInterface::class);

    $paragraph = $this->createMock(ContentEntityInterface::class);
    $paragraph->method('access')->willReturnReference($this->entityAccess);

    $entity_storage->method('create')->willReturn($paragraph);
    $entity_storage->method('load')->will($this->returnCallback([$this, 'loadEntityCallback']));

    return $entity_storage;
  }

  /**
   * Entity storage load callback.
   *
   * @param string $id
   *   Entity id.
   *
   * @return \PHPUnit\Framework\MockObject\MockObject
   *   Mocked entity.
   */
  public function loadEntityCallback($id) {
    $field_type = 'text';
    if ($id == 'paragraph.card.field_baz') {
      $field_type = 'image';
    }
    $entity = $this->createMock(FieldConfigInterface::class);
    $entity->method('getType')->willReturnReference($field_type);
    return $entity;

  }

}
