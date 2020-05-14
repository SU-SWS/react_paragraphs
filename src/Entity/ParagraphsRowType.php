<?php

namespace Drupal\react_paragraphs\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Paragraphs Row type entity.
 *
 * @ConfigEntityType(
 *   id = "paragraphs_row_type",
 *   label = @Translation("Row type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\react_paragraphs\ParagraphsRowTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\react_paragraphs\Form\ParagraphsRowTypeForm",
 *       "edit" = "Drupal\react_paragraphs\Form\ParagraphsRowTypeForm",
 *       "delete" = "Drupal\react_paragraphs\Form\ParagraphsRowTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\react_paragraphs\ParagraphsRowTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "paragraphs_row_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "paragraphs_row",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/paragraphs_row_type/{paragraphs_row_type}",
 *     "add-form" = "/admin/structure/paragraphs_row_type/add",
 *     "edit-form" = "/admin/structure/paragraphs_row_type/{paragraphs_row_type}/edit",
 *     "delete-form" = "/admin/structure/paragraphs_row_type/{paragraphs_row_type}/delete",
 *     "collection" = "/admin/structure/paragraphs_row_type"
 *   }
 * )
 */
class ParagraphsRowType extends ConfigEntityBundleBase implements ParagraphsRowTypeInterface {

  /**
   * The Paragraphs Row type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Paragraphs Row type label.
   *
   * @var string
   */
  protected $label;

}
