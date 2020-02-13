<?php

namespace Drupal\react_paragraphs\Normalizer;

use Drupal\entity_reference_revisions\Normalizer\EntityReferenceRevisionItemNormalizer;
use Drupal\react_paragraphs\Plugin\Field\FieldType\ReactParagraphs;

/**
 * Class ReactParagraphsItemNormalizer.
 *
 * @package Drupal\react_paragraphs\Normalizer
 */
class ReactParagraphsItemNormalizer extends EntityReferenceRevisionItemNormalizer {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = ReactParagraphs::class;

  /**
   * {@inheritdoc}
   */
  protected function constructValue($data, $context) {
    $value = parent::constructValue($data, $context);
    if ($value) {
      $value['settings'] = json_encode($value['settings']);
    }
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($field_item, $format = NULL, array $context = []) {
    $data = parent::normalize($field_item, $format, $context);
    $field_name = $field_item->getParent()->getName();
    $entity = $field_item->getEntity();
    $field_uri = $this->linkManager->getRelationUri($entity->getEntityTypeId(), $entity->bundle(), $field_name, $context);
    $data['_embedded'][$field_uri][0]['settings'] = $field_item->settings;

    return $data;
  }

}
