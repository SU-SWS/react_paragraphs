<?php

namespace Drupal\react_paragraphs\Entity;

use Drupal\paragraphs\Entity\Paragraph;

/**
 * Defines the Paragraphs Row entity.
 *
 * @ingroup react_paragraphs
 *
 * @ContentEntityType(
 *   id = "paragraph_row",
 *   label = @Translation("Paragraph Row"),
 *   bundle_label = @Translation("Row type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\react_paragraphs\ParagraphsRowAccessControlHandler",
 *     "storage_schema" = "Drupal\react_paragraphs\ParagraphRowStorageSchema",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm"
 *     }
 *   },
 *   base_table = "paragraph_rows_item",
 *   data_table = "paragraph_rows_item_field_data",
 *   revision_table = "paragraph_rows_revision",
 *   revision_data_table = "paragraph_rows_field_revision",
 *   translatable = TRUE,
 *   entity_revision_parent_type_field = "parent_type",
 *   entity_revision_parent_id_field = "parent_id",
 *   entity_revision_parent_field_name_field = "parent_field_name",
 *   admin_permission = "administer paragraphs row entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "bundle" = "type",
 *     "langcode" = "langcode",
 *     "revision" = "revision_id",
 *     "published" = "status"
 *   },
 *   bundle_entity_type = "paragraphs_row_type",
 *   field_ui_base_route = "entity.paragraphs_row_type.edit_form"
 * )
 */
class ParagraphRow extends Paragraph implements ParagraphsRowInterface {

}
