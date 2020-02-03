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
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();

    $self = new \ReflectionClass(get_class($this));

    $coverage_class = preg_grep('/coversDefaultClass.*?$/', explode("\n", $self->getDocComment()));
    $class = reset($coverage_class);
    $class = trim(substr($class, strrpos($class, ' ') + 1));
    $container = new ContainerBuilder();
    $container->set('entity_type.manager', $this->createMock(EntityTypeManagerInterface::class));
    $container->set('current_user', $this->createMock(AccountProxyInterface::class));

    $this->plugin = $class::create($container, [], 'foo_bar', []);
  }

}
