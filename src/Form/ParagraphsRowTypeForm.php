<?php

namespace Drupal\react_paragraphs\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\field_ui\FieldUI;
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
    $form['id']['#machine_name']['exists'] = '\Drupal\react_paragraphs\Entity\ParagraphsRowType::load';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    /** @var \Drupal\Core\Url $redirect */
    $redirect = $form_state->getRedirect();

    // Change which page the form redirects after submit since we don't wont to
    // redirect to a paragraph entity area.
    switch ($redirect->getRouteName()) {
      case 'entity.paragraphs_type.collection':
        $form_state->setRedirect('entity.paragraphs_row_type.collection');
        break;

      case 'entity.paragraph.field_ui_fields':
        $route_info = FieldUI::getOverviewRouteInfo('paragraph_row', $this->entity->id());
        $form_state->setRedirectUrl($route_info);
        break;
    }
  }

}
