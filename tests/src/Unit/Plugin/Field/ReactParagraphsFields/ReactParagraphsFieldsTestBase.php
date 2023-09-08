<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Class ReactParagraphsFieldsTestBase
 *
 * @group react_paragraphs
 */
abstract class ReactParagraphsFieldsTestBase extends UnitTestCase {

  /**
   * @var \Drupal\react_paragraphs\ReactParagraphsFieldsInterface
   */
  protected $plugin;

  /**
   * @var \Drupal\Core\DependencyInjection\ContainerBuilder
   */
  protected $container;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  protected $fieldConfig;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  protected $fieldStorage;

  /**
   * {@inheritDoc}
   */
  public function setup(): void {
    parent::setUp();

    $self = new \ReflectionClass(get_class($this));

    $coverage_class = preg_grep('/coversDefaultClass.*?$/', explode("\n", $self->getDocComment()));
    $class = reset($coverage_class);
    $class = trim(substr($class, strrpos($class, ' ') + 1));
    $this->container = new ContainerBuilder();

    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_type_manager->method('getStorage')
      ->will($this->returnCallback([$this, 'getStorageCallback']));

    $this->container->set('entity_type.manager', $entity_type_manager);
    $this->container->set('current_user', $this->createMock(AccountProxyInterface::class));

    $database = $this->createMock(Connection::class);
    $this->container->set('database', $database);

    $this->plugin = $class::create($this->container, [], 'foo_bar', []);
    \Drupal::setContainer($this->container);

    $this->fieldConfig = $this->createMock(FieldConfigInterface::class);
    $this->fieldConfig->method('getLabel')->willReturn('Foo Bar');
    $this->fieldConfig->method('getDescription')->willReturn('Description');
    $this->fieldConfig->method('isRequired')->willReturn(TRUE);

    $this->fieldStorage = $this->createMock(FieldStorageConfigInterface::class);
    $this->fieldStorage->method('getCardinality')->willReturn(1);

    $this->fieldConfig->method('getFieldStorageDefinition')->willReturn($this->fieldStorage);
  }

    /**
   * Test the field plugin.
   */
  public function testPlugin() {
    $data = $this->plugin->getFieldInfo([], $this->fieldConfig);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'foo_bar',
    ];
    $this->assertEquals($expected, $data);
  }


}
