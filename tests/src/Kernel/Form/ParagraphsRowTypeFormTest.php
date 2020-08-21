<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Form;

use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\react_paragraphs\Entity\ParagraphsRowType;

/**
 * Class ParagraphsRowTypeFormTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Form\ParagraphsRowTypeForm
 */
class ParagraphsRowTypeFormTest extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'system',
    'react_paragraphs',
    'paragraphs',
    'file',
    'user',
  ];

  /**
   * Row Entity.
   *
   * @var \Drupal\react_paragraphs\Entity\ParagraphsRowType
   */
  protected $row;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('paragraph_row');
    $this->installEntitySchema('file');
    $this->installSchema('system', ['sequences']);
    $this->installSchema('file', ['file_usage']);

    $this->row = ParagraphsRowType::create(['id' => 'row', 'label' => 'Row']);
    $this->row->save();
  }

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
