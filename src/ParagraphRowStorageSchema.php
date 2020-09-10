<?php

namespace Drupal\react_paragraphs;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\paragraphs\ParagraphStorageSchema;

/**
 * Class ParagraphRowStorageSchema extending paragraph storage schema.
 */
class ParagraphRowStorageSchema extends ParagraphStorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getEntitySchema(ContentEntityTypeInterface $entity_type, $reset = FALSE) {
    $schema = parent::getEntitySchema($entity_type, $reset);

    $schema[$this->storage->getDataTable()]['indexes'] += [
      'paragraphs__parent_fields' => [
        'parent_type',
        'parent_id',
        'parent_field_name',
      ],
    ];
    $schema[$this->storage->getRevisionDataTable()]['indexes'] += [
      'paragraphs__parent_fields' => [
        'parent_type',
        'parent_id',
        'parent_field_name',
      ],
    ];

    return $schema;
  }

}
