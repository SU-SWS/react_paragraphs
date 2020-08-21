<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Form;

use Drupal\Core\Form\FormState;

/**
 * Class ParagraphsRowTypeFormTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Form\ParagraphsRowTypeForm
 */
class ParagraphsRowTypeFormTest extends ParagraphRowFormTestBase {

  /**
   * Row type form will have a structure and changed the redirect.
   */
  public function testRowTypeForm() {
    $form_object = \Drupal::entityTypeManager()
      ->getFormObject($this->row->getEntityTypeId(), 'edit');
    $form_object->setEntity($this->row);

    $form_state = new FormState();
    $form = \Drupal::formBuilder()->buildForm($form_object, $form_state);

    $this->assertArrayNotHasKey('icon_file', $form);
    $this->assertArrayNotHasKey('message', $form);

    $form_state->setRedirect('entity.paragraphs_type.collection');
    $form_object->save($form, $form_state);
    $this->assertEquals('entity.paragraphs_row_type.collection', $form_state->getRedirect()
      ->getRouteName());
  }

}
