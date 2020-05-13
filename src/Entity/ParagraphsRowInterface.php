<?php

namespace Drupal\react_paragraphs\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Paragraphs Row entities.
 *
 * @ingroup react_paragraphs
 */
interface ParagraphsRowInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Paragraphs Row name.
   *
   * @return string
   *   Name of the Paragraphs Row.
   */
  public function getName();

  /**
   * Sets the Paragraphs Row name.
   *
   * @param string $name
   *   The Paragraphs Row name.
   *
   * @return \Drupal\react_paragraphs\Entity\ParagraphsRowInterface
   *   The called Paragraphs Row entity.
   */
  public function setName($name);

  /**
   * Gets the Paragraphs Row creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Paragraphs Row.
   */
  public function getCreatedTime();

  /**
   * Sets the Paragraphs Row creation timestamp.
   *
   * @param int $timestamp
   *   The Paragraphs Row creation timestamp.
   *
   * @return \Drupal\react_paragraphs\Entity\ParagraphsRowInterface
   *   The called Paragraphs Row entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Paragraphs Row revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Paragraphs Row revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\react_paragraphs\Entity\ParagraphsRowInterface
   *   The called Paragraphs Row entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Paragraphs Row revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Paragraphs Row revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\react_paragraphs\Entity\ParagraphsRowInterface
   *   The called Paragraphs Row entity.
   */
  public function setRevisionUserId($uid);

}
