<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Normalizer;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;

/**
 * Class ReactParagraphsItemNormalizerTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Normalizer\ReactParagraphsItemNormalizer
 */
class ReactParagraphsItemNormalizerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'field',
    'react_paragraphs',
    'node',
    'hal',
    'serialization',
    'field',
    'user',
    'paragraphs',
    'entity_reference',
    'entity_reference_revisions',
    'file',
  ];

  /**
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('field_storage_config');
    $this->installEntitySchema('field_config');
    $this->installEntitySchema('paragraph');
    $this->installEntitySchema('file');

    NodeType::create(['type' => 'page'])->save();
    $field_storage = FieldStorageConfig::create(
      [
        'field_name' => 'bar',
        'entity_type' => 'node',
        'type' => 'react_paragraphs',
      ]
    );
    $field_storage->save();

    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'page',
    ])->save();

    ParagraphsType::create([
      'label' => 'test_paragraph',
      'id' => 'test_paragraph',
    ])->save();

    $paragraph = Paragraph::create([
      'type' => 'test_paragraph',
    ]);
    $paragraph->save();

    $this->node = Node::create([
      'type' => 'page',
      'title' => 'test',
      'bar' => [
        [
          'target_id' => $paragraph->id(),
          'entity' => $paragraph,
          'settings' => ['foo' => 'bar'],
        ],
      ],
    ]);
    $this->node->save();
    $this->node = Node::load($this->node->id());
  }

  public function testNormalizer() {
    $this->assertTrue(TRUE);

    /** @var \Symfony\Component\Serializer\SerializerInterface $serializer */
    $serializer = \Drupal::service('serializer');
    $output = $serializer->serialize($this->node, 'hal_json');
    $position = strpos($output, '"settings":{"foo":"bar"}}]},');
    $this->assertTrue($position > 1);

    $node_class = \Drupal::entityTypeManager()->getDefinition('node')->getClass();
    $object = $serializer->deserialize($output, $node_class, 'hal_json', ['request_method' => 'POST']);
    $field_value = $object->get('bar')->getValue();

    $this->assertArrayHasKey('foo', $field_value[0]['settings']);
    $this->assertCount(1, $field_value[0]['settings']);
  }

}
