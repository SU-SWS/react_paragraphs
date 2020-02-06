<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;

/**
 * Class ReactParagraphsFieldTestBase
 *
 * @package Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\FieldWidget
 */
abstract class ReactParagraphsFieldTestBase extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'system',
    'react_paragraphs',
    'field',
    'paragraphs',
    'entity_reference_revisions',
    'editor',
    'node',
    'user',
    'filter',
    'file'
  ];

  /**
   * @var \Drupal\field\FieldStorageConfigInterface
   */
  protected $fieldStorage;

  /**
   * @var \Drupal\field\FieldConfigInterface
   */
  protected $fieldConfig;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('field_storage_config');
    $this->installEntitySchema('paragraph');
    $this->installEntitySchema('entity_view_display');
    $this->installEntitySchema('file');
    $this->installSchema('system', ['sequences']);
        $this->installSchema('file', ['file_usage']);

    NodeType::create(['type' => 'page', 'name' => 'Page'])->save();

    $this->fieldStorage = FieldStorageConfig::create(
      [
        'field_name' => 'foo',
        'entity_type' => 'node',
        'type' => 'react_paragraphs',
        'settings' => [
          'target_type' => 'paragraph',
        ],
      ]
    );
    $this->fieldStorage->save();

    $this->fieldConfig = FieldConfig::create([
      'field_storage' => $this->fieldStorage,
      'bundle' => 'page',
    ]);
    $this->fieldConfig->save();

    EntityViewDisplay::create([
      'status' => TRUE,
      'targetEntityType' => 'node',
      'bundle' => 'page',
      'mode' => 'default',
    ])->setComponent('foo')->save();

    EntityFormDisplay::create([
      'status' => TRUE,
      'targetEntityType' => 'node',
      'bundle' => 'page',
      'mode' => 'default',
    ])->setComponent('foo')
      ->removeComponent('created')
      ->save();

    DateFormat::create([
      'id' => 'short',
      'label' => 'Llama',
      'pattern' => 'F d, Y',
    ])->save();
  }

}
