<?php

namespace Drupal\react_paragraphs\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Paragraphs Row entities.
 */
class ParagraphsRowViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
