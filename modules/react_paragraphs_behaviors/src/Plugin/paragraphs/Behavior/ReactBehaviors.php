<?php

namespace Drupal\react_paragraphs_behaviors\Plugin\paragraphs\Behavior;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\ParagraphsBehaviorBase;

/**
 * Class ReactBehaviors
 *
 * @ParagraphsBehavior(
 *   id = "react_pagraphs",
 *   admin_label = @Translation("React Paragrpahs"),
 *   deriver = "Drupal\react_paragraphs_behaviors\Plugin\Derivative\ReactBehaviorsDeriver"
 * )
 */
class ReactBehaviors extends ParagraphsBehaviorBase {

  /**
   * {@inheritDoc}
   */
  public function buildBehaviorForm(ParagraphInterface $paragraph, array &$form, FormStateInterface $form_state) {
    $form = parent::buildBehaviorForm($paragraph, $form, $form_state);

    foreach ($this->pluginDefinition['config'] as $config_key => $config_field) {
      foreach ($config_field as $key => $item) {
        $form[$config_key]["#$key"] = $item;

        if ($value = $paragraph->getBehaviorSetting($this->getPluginId(), $config_key)) {
          $form[$config_key]["#default_value"] = $value;
        }
      }
    }
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function view(array &$build, Paragraph $paragraph, EntityViewDisplayInterface $display, $view_mode) {
    // todo: implement the hook that does the stuff.
  }

}