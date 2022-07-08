<?php

namespace Drupal\Tests\react_paragraphs\Unit\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\media\MediaTypeInterface;

/**
 * Class BooleanTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields\EntityReference
 */
class EntityReferenceTest extends ReactParagraphsFieldsTestBase {

  /**
   * Test the field plugin.
   */
  public function testPluginMediaLibrary() {
    $field_element['widget']['media_library_selection'] = '';
    $data = $this->plugin->getFieldInfo($field_element, $this->fieldConfig);

    $expected = [
      'cardinality' => 1,
      'help' => 'Description',
      'label' => 'Foo Bar',
      'required' => TRUE,
      'weight' => 0,
      'widget_type' => 'media_library',
      'target_bundles' => ['image' => 'Image'],
    ];
    $this->assertEquals($expected, $data);
  }

  /**
   * Entity type manager get storage callback.
   */
  public function getStorageCallback() {
    $media_type = $this->createMock(MediaTypeInterface::class);
    $media_type->method('label')->willReturn('Image');

    $media_storage = $this->createMock(EntityStorageInterface::class);
    $media_storage->method('loadMultiple')->willReturn(['image' => $media_type]);
    return $media_storage;
  }

}
