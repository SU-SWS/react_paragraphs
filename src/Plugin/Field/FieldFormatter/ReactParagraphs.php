<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Template\Attribute;
use Drupal\entity_reference_revisions\Plugin\Field\FieldFormatter\EntityReferenceRevisionsEntityFormatter;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Plugin implementation of the 'react_paragraphs' formatter.
 *
 * @deprecated Use EntityReferenceRevisionsEntityFormatter instead.
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
