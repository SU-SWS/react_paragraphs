<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\react_paragraphs\Attribute\ReactParagraphsFields;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;

/**
 * Default field plugin.
 */
#[ReactParagraphsFields(
  id: 'default',
)]
class DefaultPlugin extends ReactParagraphsFieldsBase {

}
