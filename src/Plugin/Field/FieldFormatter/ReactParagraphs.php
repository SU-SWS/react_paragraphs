<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldFormatter;

use Drupal\entity_reference_revisions\Plugin\Field\FieldFormatter\EntityReferenceRevisionsEntityFormatter;

/**
 * Plugin implementation of the 'react_paragraphs' formatter.
 *
 * This plugin is deprecated and is only used for upgrade process. It will be
 * Marked as deprecated and removed in a later release.
 *
 * @FieldFormatter(
 *   id = "react_paragraphs",
 *   label = @Translation("DEPRECATED: React Paragraphs"),
 *   field_types = {
 *     "react_paragraphs",
 *     "entity_reference_revisions"
 *   }
 * )
 */
class ReactParagraphs extends EntityReferenceRevisionsEntityFormatter {

}
