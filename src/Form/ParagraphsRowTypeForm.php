<?php

namespace Drupal\react_paragraphs\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Form\ParagraphsTypeForm;

/**
 * Class ParagraphsRowTypeForm.
 */
class ParagraphsRowTypeForm extends ParagraphsTypeForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    unset($form['icon_file'], $form['message']);
    return $form;
  }

}
