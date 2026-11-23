<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field;

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\react_paragraphs\Entity\ParagraphRow;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Tests search reindexing of nodes with React paragraphs fields.
 *
 * @group react_paragraphs
 */
class ReactParagraphsFieldCronTest extends ReactParagraphsFieldTestBase {

  use UserCreationTrait;

  /**
   * Node with paragraph rows that is indexed.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * Marking a node with paragraph rows for reindexing does not fail.
   */
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
    \Drupal::service('search.index')->markForReindex('node_search', $this->node->id());
  }

}
