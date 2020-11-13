<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\ReactParagraphsFields;

use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\node\Entity\Node;
use Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\ReactParagraphsFieldTestBase;

/**
 * Class EntityReferenceTest
 *
 * @package Drupal\Tests\react_paragraphs\Kernel\Plugin\Field\ReactParagraphsFields
 */
class EntityReferenceTest extends ReactParagraphsFieldTestBase {

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    Node::create(['type' => 'page', 'title' => 'Foo'])->save();
  }

  /**
   * An autocomplete field widget will be used as a select list in react.
   */
  public function testAutocomplete() {
    /** @var \Drupal\react_paragraphs\ReactParagraphsFieldsManager $plugin_manager */
    $plugin_manager = \Drupal::service('plugin.manager.react_paragraphs_fields');
    $plugin = $plugin_manager->createInstance('entity_reference');

    $field_element['widget'][0]['target_id']['#type'] = 'entity_autocomplete';
    $data = $plugin->getFieldInfo($field_element, $this->getFieldConfig());
    $this->assertEqual($data['widget_type'], 'entity_reference_autocomplete');
    $this->assertCount(1, $data['options']);
  }

  /**
   * A select list widget will pass the options up to react.
   */
  public function testSelectList() {
    /** @var \Drupal\react_paragraphs\ReactParagraphsFieldsManager $plugin_manager */
    $plugin_manager = \Drupal::service('plugin.manager.react_paragraphs_fields');
    $plugin = $plugin_manager->createInstance('entity_reference');

    $field_element['widget']['#type'] = 'select';
    $data = $plugin->getFieldInfo($field_element, $this->getFieldConfig());
    $this->assertEqual($data['widget_type'], 'entity_reference_autocomplete');
    $this->assertCount(1, $data['options']);
  }

  /**
   * Create and return a mock field config entity.
   *
   * @return \Drupal\field\FieldConfigInterface
   *   Mocked entity.
   */
  protected function getFieldConfig(){
    $field_storage = $this->createMock(FieldStorageConfigInterface::class);
    $field_storage->method('getCardinality')->willReturn(1);

    $field_config = $this->createMock(FieldConfigInterface::class);
    $field_config->method('getFieldStorageDefinition')->willReturn($field_storage);

    $field_config->method('getSetting')
      ->will($this->returnCallback([$this,'getSettingCallback']));
    return $field_config;
  }

  /**
   * Field storage getSetting callback.
   */
  public function getSettingCallback($key) {
    switch ($key) {
      case 'handler':
        return 'default:node';

      case 'handler_settings':
        return ['target_bundles' => ['page']];
    }
  }

}
