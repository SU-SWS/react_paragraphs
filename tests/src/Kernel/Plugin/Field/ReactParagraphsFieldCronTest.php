<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
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

  protected function testIndexing() {
    \Drupal::service('module_installer')->install(['search']);

    \Drupal::service('file_system')->copy($this->root . '/core/misc/druplicon.png', 'public://example.jpg');
    $image = File::create(['uri' => 'public://example.jpg']);
    $image->save();

    ParagraphsType::create([
      'id' => 'card',
      'label' => 'Card',
      'icon_uuid' => $image->uuid(),
    ])->save();

    $field_storage = FieldStorageConfig::create(
      [
        'field_name' => 'bar',
        'entity_type' => 'paragraph',
        'type' => 'text',
      ]
    );
    $field_storage->save();
    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'card',
    ])->save();

    $paragraph1 = Paragraph::create(['type' => 'card']);
    $paragraph1->save();

    $paragraph2 = Paragraph::create(['type' => 'card']);
    $paragraph2->save();

    $user = $this->createUser();

    $this->node = Node::create([
      'type' => 'page',
      'title' => 'node',
      'foo' => [
        [
          'target_id' => $paragraph1->id(),
          'entity' => $paragraph1,
          'settings' => '',
        ],
        [
          'target_id' => $paragraph2->id(),
          'entity' => $paragraph2,
          'settings' => json_encode(['row' => 1, 'index' => 0, 'width' => 12, 'admin_title' => '']),
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
