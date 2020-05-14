<?php

/**
 * @file
 * Hooks specific to the react_paragraph module.
 *
 * @codeCoverageIgnore
 */

use Drupal\Field\Entity\FieldConfig;

/**
 * Alter the info before being sent to the react widget.
 *
 * @codeCoverageIgnore
 *
 * @param array $field_element
 *   The form field element that is being transformed into react data.
 * @param \Drupal\Field\Entity\FieldConfig $field_config
 *   The field configuration object.
 * @param array $info
 *   The array of data being passed to the react widget.
 *
 * @see \Drupal\react_paragraphs\Plugin\rest\resource\ReactParagraphsResource::get
 */
function hook_react_paragraphs_getfieldinfo_post_alter(array $field_element, FieldConfig $field_config, array $info) {

  // Only alter data for the news_views view field.
  if ($field_config->get('field_name') !== "field_foo") {
    return;
  }

  // Remove something.
  unset($info['foo']['thing']);

  // Add something.
  $info['my_field']['values'] = ['foo', 'bar'];

}
