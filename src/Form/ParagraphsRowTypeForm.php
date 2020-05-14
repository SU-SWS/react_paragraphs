<?php

namespace Drupal\react_paragraphs\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ParagraphsRowTypeForm.
 */
class ParagraphsRowTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $paragraphs_row_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $paragraphs_row_type->label(),
      '#description' => $this->t("Label for the Paragraphs Row type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $paragraphs_row_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\react_paragraphs\Entity\ParagraphsRowType::load',
      ],
      '#disabled' => !$paragraphs_row_type->isNew(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $paragraphs_row_type = $this->entity;
    $status = $paragraphs_row_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Paragraphs Row type.', [
          '%label' => $paragraphs_row_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Paragraphs Row type.', [
          '%label' => $paragraphs_row_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($paragraphs_row_type->toUrl('collection'));
  }

}
