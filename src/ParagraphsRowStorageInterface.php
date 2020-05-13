<?php

namespace Drupal\react_paragraphs;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\react_paragraphs\Entity\ParagraphsRowInterface;

/**
 * Defines the storage handler class for Paragraphs Row entities.
 *
 * This extends the base storage class, adding required special handling for
 * Paragraphs Row entities.
 *
 * @ingroup react_paragraphs
 */
interface ParagraphsRowStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Paragraphs Row revision IDs for a specific Paragraphs Row.
   *
   * @param \Drupal\react_paragraphs\Entity\ParagraphsRowInterface $entity
   *   The Paragraphs Row entity.
   *
   * @return int[]
   *   Paragraphs Row revision IDs (in ascending order).
   */
  public function revisionIds(ParagraphsRowInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Paragraphs Row author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Paragraphs Row revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\react_paragraphs\Entity\ParagraphsRowInterface $entity
   *   The Paragraphs Row entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ParagraphsRowInterface $entity);

  /**
   * Unsets the language for all Paragraphs Row with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
