<?php

namespace Drupal\react_paragraphs\Normalizer;

use Drupal\paragraphs\ParagraphInterface;
use Drupal\serialization\Normalizer\ContentEntityNormalizer;

/**
 * Class ParagraphBehaviorsNormalizer.
 *
 * @package Drupal\react_paragraphs\Normalizer
 */
class ParagraphBehaviorsNormalizer extends ContentEntityNormalizer {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = ParagraphInterface::class;

  /**
   * {@inheritDoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $return = parent::normalize($object, $format, $context);
    if (!empty($return['behavior_settings'][0]['value']) && !is_array($return['behavior_settings'][0]['value'])) {
      $return['behavior_settings'][0]['value'] = unserialize($return['behavior_settings'][0]['value']);
    }
    return $return;
  }

}
