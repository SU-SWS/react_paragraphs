<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Session\AccountInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\file\Entity\File;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\react_paragraphs\Entity\ParagraphRow;
use Drupal\react_paragraphs\Entity\ParagraphsRowType;
use Drupal\user\Entity\Role;

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
    'file',
    'text',
    'hal',
    'serialization',
    'field_ui',
  ];

  protected $nodeType;

  protected $rowType;

  protected $paragraphType;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('field_storage_config');
    $this->installEntitySchema('paragraph');
    $this->installEntitySchema('paragraph_row');
    $this->installEntitySchema('entity_view_display');
    $this->installEntitySchema('file');
    $this->installSchema('system', ['sequences']);
    $this->installSchema('file', ['file_usage']);

    Role::create(['id' => AccountInterface::ANONYMOUS_ROLE])->save();
    user_role_grant_permissions(AccountInterface::ANONYMOUS_ROLE, ['access content']);

    $this->createParagraphTypes();
    $this->createParagraphRow();
    $this->createNodeType();

    DateFormat::create([
      'id' => 'short',
      'label' => 'short',
      'pattern' => 'F d, Y',
    ])->save();
    DateFormat::create([
      'id' => 'medium',
      'label' => 'medium',
      'pattern' => 'F d, Y',
    ])->save();
  }

  protected function createParagraphTypes() {
    \Drupal::service('file_system')
      ->copy($this->root . '/core/misc/druplicon.png', 'public://example.jpg');

    $image = File::create(['uri' => 'public://example.jpg']);
    $image->save();

    $this->paragraphType = ParagraphsType::create([
      'id' => 'card',
      'label' => 'Card',
      'icon_uuid' => $image->uuid(),
    ]);
    $this->paragraphType->save();

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
  }

  protected function createParagraphRow() {
    $this->rowType = ParagraphsRowType::create([
      'id' => 'row',
      'label' => 'Row',
    ]);
    $this->rowType->save();

    $fieldStorage = FieldStorageConfig::create(
      [
        'field_name' => 'foo',
        'entity_type' => 'paragraph_row',
        'type' => 'entity_reference_revisions',
        'settings' => ['target_type' => 'paragraph'],
      ]
    );
    $fieldStorage->save();

    $fieldConfig = FieldConfig::create([
      'field_storage' => $fieldStorage,
      'bundle' => 'row',
    ]);
    $fieldConfig->save();
  }

  protected function createNodeType() {
    $this->nodeType = NodeType::create(['type' => 'page', 'name' => 'Page']);
    $this->nodeType->save();
    /** @var \Drupal\field\FieldStorageConfigInterface $fieldStorage */
    $fieldStorage = FieldStorageConfig::create(
      [
        'field_name' => 'foo',
        'entity_type' => 'node',
        'type' => 'entity_reference_revisions',
        'settings' => ['target_type' => 'paragraph_row'],
      ]
    );
    $fieldStorage->save();

    $this->fieldConfig = FieldConfig::create([
      'field_storage' => $fieldStorage,
      'bundle' => 'page',
      'settings' => ['handler_settings' => ['target_bundles' => ['row']]],
    ]);
    $this->fieldConfig->save();

    /** @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display */
    $display = EntityViewDisplay::create([
      'status' => TRUE,
      'targetEntityType' => 'node',
      'bundle' => 'page',
      'mode' => 'default',
    ]);
    $display->setComponent('foo', ['type' => 'react_paragraphs']);
    $display->removeComponent('created');
    $display->save();

    EntityFormDisplay::create([
      'status' => TRUE,
      'targetEntityType' => 'node',
      'bundle' => 'page',
      'mode' => 'default',
    ])->setComponent('foo', ['type' => 'react_paragraphs'])
      ->removeComponent('created')
      ->save();
  }

}
