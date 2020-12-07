<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Form;

use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\react_paragraphs\Entity\ParagraphsRowType;

/**
 * Class ParagraphRowFormTestBase.
 *
 * @package Drupal\Tests\react_paragraphs\Kernel\Form
 */
abstract class ParagraphRowFormTestBase extends KernelTestBase {

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
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('paragraph_row');
    $this->installEntitySchema('file');
    $this->installSchema('system', ['sequences']);
    $this->installSchema('file', ['file_usage']);

    $this->row = ParagraphsRowType::create(['id' => 'row', 'label' => 'Row']);
    $this->row->save();
  }

}
