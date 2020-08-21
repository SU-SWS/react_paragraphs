<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\FieldWidget;

use Drupal\Core\Form\FormState;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\react_paragraphs\Entity\ParagraphRow;
use Drupal\react_paragraphs\Plugin\Field\FieldWidget\ReactParagraphs;
use Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\ReactParagraphsFieldTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Class ReactParagraphsFieldTestBase.
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\FieldWidget\ReactParagraphs
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


    $paragraph1 = Paragraph::create(['type' => 'card']);
    $paragraph1->save();

    $paragraph2 = Paragraph::create(['type' => 'card']);
    $paragraph2->save();

    $user = $this->createUser();
    $row = ParagraphRow::create([
      'type' => 'row',
      'foo' => [
        [
          'target_id' => $paragraph1->id(),
          'entity' => $paragraph1,
          'settings' => '',
        ],
        [
          'target_id' => $paragraph2->id(),
          'entity' => $paragraph2,
        ],
      ],
    ]);
    $row->save();
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
  }

  /**
   * Test the widget is going to output expected settings.
   */
  public function testReactFieldWidget() {

    /** @var \Drupal\Core\Entity\EntityFormBuilderInterface $form_builder */
    $form_builder = \Drupal::service('entity.form_builder');
    $form = $form_builder->getForm(Node::load($this->node->id()));
    $attachments = $form['foo']['widget']['container']['value']['#attached']['drupalSettings']['reactParagraphs'];

    $expected = [
      0 => [
        'fieldId' => 'react-foo',
        'inputId' => 'react-foo-input',
        'rowBundle' => 'row',
        'tools' => [
          0 => [
            'id' => 'card',
            'label' => 'Card',
            'description' => NULL,
            'minWidth' => 1,
          ],
        ],
        'items' => [
          0 => [
            'row' => [
              'target_id' => '1',
              'entity' => [
                'type' => [
                  0 => [
                    'target_id' => 'row',
                  ],
                ],
              ],
            ],
            'rowItems' => [
              0 => [
                'target_id' => '1',
                'entity' => [
                  'type' => [
                    0 => [
                      'target_id' => 'card',
                    ],
                  ],
                ],
                'settings' => [
                  'width' => NULL,
                  'admin_title' => NULL,
                ],
              ],
            ],
          ],
        ],
        'itemsPerRow' => 1,
        'resizableItems' => FALSE,
      ],
    ];

    $this->assertEqual('card', $attachments[0]['tools'][0]['id']);
    $this->assertNotEmpty($attachments[0]['tools'][0]['icon']);
    unset($attachments[0]['tools'][0]['icon']);

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
    $this->assertCount(1, $settings_form);

    $summary = $widget->settingsSummary();
    $this->assertCount(1, $summary);
    $this->assertEqual(trim((string) $summary[0]), 'Equal Widths');

    $value = [];
    $values['container']['value'] = json_encode($value);
    $this->assertEmpty($widget->massageFormValues($values, $form, $form_state));

    $value = $this->getValidValue();
    $values['container']['value'] = json_encode($value);
    $massaged_values = $widget->massageFormValues($values, $form, $form_state);
    $this->assertNotEmpty($massaged_values);
    $this->assertCount(2, $massaged_values);
  }

  /**
   * Get a valid value that would be submitted from react data.
   *
   * @return array
   *   React data.
   */
  protected function getValidValue() {
    $paragraph = Paragraph::create(['type' => 'card']);
    $paragraph->save();
    return [
      'rowOrder' => ['row-0', 'row-1'],
      'rows' => [
        'row-0' => [
          'id' => 'row-0',
          'items' => [
            'new-7b69c03c-e0e2-408c-b950-85e205660580' => [
              'id' => 'new-7b69c03c-e0e2-408c-b950-85e205660580',
              'index' => 0,
              'width' => 12,
              'admin_title' => 'Card',
              'entity' => ['type' => [['target_id' => 'card']]],
            ],
          ],
          'itemsOrder' => ['new-7b69c03c-e0e2-408c-b950-85e205660580'],
          'isDropDisabled' => TRUE,
          'entity' => ['type' => [['target_id' => 'row']]],
          'target_id' => NULL,
        ],
        'row-1' => [
          'id' => 'row-1',
          'items' => [
            $paragraph->uuid() => [
              'id' => $paragraph->uuid(),
              'index' => 0,
              'width' => 12,
              'admin_title' => 'Text Area',
              'target_id' => $paragraph->id(),
              'entity' => ['type' => [['target_id' => 'card']]],
            ],
          ],
          'itemsOrder' => [$paragraph->uuid()],
          'isDropDisabled' => TRUE,
          'entity' => ['type' => [['target_id' => 'row']]],
          'target_id' => NULL,
        ],
      ],
    ];
  }

  /**
   * Test cron functions correctly.
   */
  public function testCron() {
    \Drupal::service('module_installer')->install(['search']);
    foreach (Paragraph::loadMultiple() as $entity) {
      $entity->delete();
    }
    $search_page_repository = \Drupal::service('search.search_page_repository');
    foreach ($search_page_repository->getIndexableSearchPages() as $entity) {
      $entity->getPlugin()->updateIndex();
    }
  }

}
