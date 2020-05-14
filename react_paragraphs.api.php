<?php

use Drupal\Field\Entity\FieldConfig;

/**
 * Alter the info before being sent to the react widget.
 *
 * @param array $field_element
 * @param \Drupal\Field\Entity\FieldConfig $field_config
 * @param array $info
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
