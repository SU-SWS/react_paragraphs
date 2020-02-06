<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\ReactParagraphsFieldTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Class ReactParagraphsFieldTestBase
 *
 * @package Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\FieldWidget
 */
class ReactParagraphsFormatterTest extends ReactParagraphsFieldTestBase {

  use UserCreationTrait;

  /**
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();

    ParagraphsType::create([
      'id' => 'card',
      'label' => 'Card',
    ])->save();

    EntityViewDisplay::create([
      'status' => TRUE,
      'targetEntityType' => 'paragraph',
      'bundle' => 'card',
      'mode' => 'default',
    ])->removeComponent('created')
      ->save();

    $paragraph1 = Paragraph::create(['type' => 'card', 'status' => 1]);
    $paragraph1->save();

    /** @var Paragraph $paragraph2 */
    $paragraph2 = Paragraph::create(['type' => 'card', 'status' => 1]);
    $paragraph2->save();

    $user = $this->createUser();

    $this->node = Node::create([
      'type' => 'page',
      'title' => 'node',
      'status' => 1,
      'foo' => [
        [
          'target_id' => $paragraph1->id(),
          'entity' => $paragraph1,
          'settings' => '',
        ],
        [
          'target_id' => $paragraph2->id(),
          'entity' => $paragraph2,
          'settings' => json_encode(['row' => 1, 'index' => 0, 'width' => 6, 'admin_title' => '']),
        ],
      ],
    ]);
    $this->node->setOwner($user);
    $this->node->save();
  }

  public function testReactFieldFormatter() {
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
    $this->node = \Drupal::entityTypeManager()->getStorage('node')->load($this->node->id());
    $display = $view_builder->view($this->node, 'default');
    $display = \Drupal::service('renderer')->renderPlain($display);

    preg_match_all('/react-paragraphs-row/', (string) $display, $rows);
    $this->assertCount(2, $rows[0]);

    preg_match_all('/paragraph-item/', (string) $display, $rows);
    $this->assertCount(2, $rows[0]);

    preg_match_all('/data-react-columns="6"/', (string) $display, $rows);
    $this->assertCount(2, $rows[0]);

  }

}
