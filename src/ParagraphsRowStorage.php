<?php

namespace Drupal\react_paragraphs;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class ParagraphsRowStorage extends SqlContentEntityStorage implements ParagraphsRowStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ParagraphsRowInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {paragraphs_row_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {paragraphs_row_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ParagraphsRowInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {paragraphs_row_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('paragraphs_row_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
