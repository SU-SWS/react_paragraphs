<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
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
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();

    $self = new \ReflectionClass(get_class($this));

    $coverage_class = preg_grep('/coversDefaultClass.*?$/', explode("\n", $self->getDocComment()));
    $class = reset($coverage_class);
    $class = trim(substr($class, strrpos($class, ' ') + 1));
    $this->container = new ContainerBuilder();
    $this->container->set('entity_type.manager', $this->createMock(EntityTypeManagerInterface::class));
    $this->container->set('current_user', $this->createMock(AccountProxyInterface::class));

    $this->plugin = $class::create($this->container, [], 'foo_bar', []);
    \Drupal::setContainer($this->container);
  }

}
