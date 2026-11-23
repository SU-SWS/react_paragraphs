<?php

namespace Drupal\react_paragraphs\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a React paragraphs field plugin annotation object.
 *
 * @Annotation
 */
class ReactParagraphsFields extends Plugin {

  /**
   * Field types the plugin provides React widget data for.
   *
   * @var string[]
   */
  public $field_types = [];

}
