<?php

/**
 * @file
 * Contains paragraphs_row.page.inc.
 *
 * Page callback for Paragraphs Row entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Paragraphs Row templates.
 *
 * Default template: paragraphs_row.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_paragraphs_row(array &$variables) {
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
