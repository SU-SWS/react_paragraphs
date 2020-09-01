<?php


namespace Drupal\Tests\react_paragraphs\Kernel\Form;

use Drupal\Core\Form\FormState;
use Drupal\react_paragraphs\Entity\ParagraphsRowType;

/**
 * Class ParagraphsRowTypeFormTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Form\ParagraphsRowTypeForm
 */
class ParagraphsRowTypeDeleteFormTest extends ParagraphRowFormTestBase {

  /**
   * Row type form will have a structure and changed the redirect.
   */
  public function testRowTypeForm() {
    /** @var \Drupal\Core\Entity\EntityConfirmFormBase $form_object */
    $form_object = \Drupal::entityTypeManager()
      ->getFormObject($this->row->getEntityTypeId(), 'delete');
    $form_object->setEntity($this->row);
    $this->assertContains('Are you sure', $form_object->getQuestion()
      ->render());

    $this->assertContains('entity.paragraphs_row_type.collection', $form_object->getCancelUrl()->getRouteName());

    $this->assertContains('Delete', $form_object->getConfirmText()
      ->render());

    $form_state = new FormState();
    $form = \Drupal::formBuilder()->buildForm($form_object, $form_state);

    $this->assertNotEmpty(ParagraphsRowType::loadMultiple());
    $form_object->submitForm($form, $form_state);
    $this->assertEmpty(ParagraphsRowType::loadMultiple());
  }

}
