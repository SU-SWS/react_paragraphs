<?php

namespace Drupal\react_paragraphs\Plugin\paragraphs\Behavior;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\ParagraphsBehaviorBase;

/**
 * A paragraphs behavior plugin to provide width and admin label storage.
 *
 * @ParagraphsBehavior(
 *   id = "react",
 *   label = "React Paragraphs",
 *   description = "Some storage capabilities for react paragraphs widget."
 * )
 */
class ReactBehavior extends ParagraphsBehaviorBase {

  /**
   * {@inheritDoc}
   */
  public function view(array &$build, Paragraph $paragraph, EntityViewDisplayInterface $display, $view_mode) {
    // Nothing to modify on the paragraph itself.
  }

}
