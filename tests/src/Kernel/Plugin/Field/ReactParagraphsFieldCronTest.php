<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\react_paragraphs\Entity\ParagraphRow;
use Drupal\search\Entity\SearchPage;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Class ReactParagraphsFieldCronTest
 *
 * @group react_paragraphs
 */
class ReactParagraphsFieldCronTest extends ReactParagraphsFieldTestBase {

  use UserCreationTrait;

  /**
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  public function testIndexing() {
    \Drupal::service('module_installer')->install(['search']);

    $paragraph = Paragraph::create(['type' => 'row']);
    $paragraph->save();

    $row = ParagraphRow::create(['type' => 'card', 'foo' => ['target_id' => $paragraph->id(), 'entity' => $paragraph]]);
    $row->save();

    $user = $this->createUser();

    $this->node = Node::create([
      'type' => 'page',
      'title' => 'node',
      'foo' => [
        [
          'target_id' => $row->id(),
          'entity' => $row,
        ],
      ],
    ]);
    $this->node->setOwner($user);
    $this->node->save();

    \Drupal::database()
      ->insert('search_dataset')
      ->fields([
        'sid' => $this->node->id(),
        'langcode' => 'en',
        'type' => 'node_search',
        'data' => '',
      ])
      ->execute();
    node_reindex_node_search($this->node->id());
  }

}
