<?php

namespace Drupal\react_paragraphs\Plugin\paragraphs\Behavior;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\ParagraphsBehaviorBase;

/**
 * Class ReactBehavior
 *
 * @ParagraphsBehavior(
 *   id = "react",
 *   label = "React Paragraphs",
 *   description = "STuff i'm not sure"
 * )
 */
class ReactBehavior extends ParagraphsBehaviorBase {

  /**
   * {@inheritDoc}
   */
  public function view(array &$build, Paragraph $paragraph, EntityViewDisplayInterface $display, $view_mode) {
    if ($width = $paragraph->getBehaviorSetting('react', 'width')) {
      $build['#attributes']['data-react-columns'] = $width;
      $build['#attached']['library'][] = 'react_paragraphs/field_formatter';
    }
  }

}
