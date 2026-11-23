<?php

namespace Drupal\react_paragraphs\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;

/**
 * Defines a React paragraphs field plugin attribute.
 *
 * Plugins describe the form data the React paragraphs widget needs for the
 * given field types.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ReactParagraphsFields extends Plugin {

  /**
   * Constructs a React paragraphs field plugin attribute.
   *
   * @param string $id
   *   The plugin ID.
   * @param string[] $field_types
   *   Field types the plugin provides React widget data for.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   */
  public function __construct(
    public readonly string $id,
    public readonly array $field_types = [],
    public readonly ?string $deriver = NULL,
  ) {}

}
