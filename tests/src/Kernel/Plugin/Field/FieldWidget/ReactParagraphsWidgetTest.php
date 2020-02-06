<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\FieldWidget;

use Drupal\Core\Form\FormState;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\react_paragraphs\Plugin\Field\FieldWidget\ReactParagraphs;
use Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\ReactParagraphsFieldTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Class ReactParagraphsFieldTestBase
 *
 * @package Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\FieldWidget
 */
class ReactParagraphsWidgetTest extends ReactParagraphsFieldTestBase {

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

    $config = [
      'field_definition' => $this->fieldConfig,
      'settings' => [],
      'third_party_settings' => [],
    ];
    $widget = ReactParagraphs::create(\Drupal::getContainer(), $config, 'react_paragraphs', []);

    $form = [];
    $form_state = new FormState();
    $settings_form = $widget->settingsForm($form, $form_state);
    $this->assertCount(2, $settings_form);
    $this->assertArrayHasKey('items_per_row', $settings_form);
    $this->assertArrayHasKey('resizable', $settings_form);

    $summary = $widget->settingsSummary();
    $this->assertCount(1, $summary);
    $this->assertEqual(trim((string) $summary[0]), '1 item per row');

    $value = [];
    $values['container']['value'] = json_encode($value);
    $this->assertEmpty($widget->massageFormValues($values, $form, $form_state));

    $value = $this->getValidValue();
    $values['container']['value'] = json_encode($value);
    $massaged_values = $widget->massageFormValues($values, $form, $form_state);
    $this->assertNotEmpty($massaged_values);
    $this->assertCount(3, $massaged_values);
  }

  /**
   * Get a valid value that would be submitted from react data.
   *
   * @return array
   *   React data.
   */
  protected function getValidValue() {
    return [
      'rowOrder' => ['row-1', 'row-2'],
      'rows' => [
        'row-1' => [
          'itemsOrder' => ['item-1', 'item-2'],
          'items' => [
            'item-1' => [
              'id' => 'item-1',
              'target_id' => 1,
              'admin_title' => 'Foo',
              'width' => 6,
              'entity' => [
                'bar' => [['value' => 'Foo Bar']],
              ],
            ],
            'item-2' => [
              'id' => 'item-2',
              'admin_title' => 'Bar',
              'width' => 6,
              'entity' => [
                'type' => 'card',
              ],
            ],
          ],
        ],
        'row-2' => [
          'itemsOrder' => ['item-3', 'item-4'],
          'items' => [
            'item-3' => [
              'id' => 'item-2',
              'admin_title' => 'Baz',
              'width' => 12,
              'entity' => [
                'type' => 'card',
              ],
            ],
            'item-4' => [
              'id' => 'item-4',
              'admin_title' => 'Baz',
              'width' => 12,
              'entity' => [],
            ],

          ],
        ],
      ],
    ];
  }

}
