<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field;

use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Class ReactParagraphsFieldTestBase
 *
 * @package Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\FieldWidget
 */
class ReactParagraphsFieldTest extends ReactParagraphsFieldTestBase {

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
    \Drupal::service('file_system')->copy($this->root . '/core/misc/druplicon.png', 'public://example.jpg');
    $image = File::create(['uri' => 'public://example.jpg']);
    $image->save();

    ParagraphsType::create([
      'id' => 'card',
      'label' => 'Card',
      'icon_uuid' => $image->uuid(),
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
  }

  public function testReactFieldWidget() {
    /** @var \Drupal\Core\Entity\EntityFormBuilderInterface $form_builder */
    $form_builder = \Drupal::service('entity.form_builder');
    $form = $form_builder->getForm($this->node);
    $attachments = $form['foo']['widget']['container']['value']['#attached']['drupalSettings']['reactParagraphs'];

    $expected = [
      [
        'fieldId' => 'react-foo',
        'inputId' => 'react-foo-input',
        'tools' => [
          'card' => [
            'label' => 'Card',
            'weight' => 0,
          ],
        ],
        'items' => [
          0 => [
            'target_id' => '1',
            'settings' => [
              'row' => 0,
              'index' => 0,
              'width' => 12,
              'admin_title' => 'Card',
            ],
            'target_revision_id' => '1',
          ],
          1 => [
            'target_id' => '2',
            'settings' => [
              'row' => 1,
              'index' => 0,
              'width' => 12,
              'admin_title' => '',
            ],
            'target_revision_id' => '2',
          ],
        ],
        'itemsPerRow' => 1,
        'resizableItems' => FALSE,
      ],
    ];
    $this->assertNotEmpty($attachments[0]['tools']['card']['icon']);
    unset($attachments[0]['tools']['card']['icon']);
    $this->assertSame($expected, $attachments);

  }

}
