<?php

namespace Drupal\Tests\react_paragraphs\Kernel\Controller;

use Drupal\KernelTests\KernelTestBase;
use Drupal\react_paragraphs\Controller\ReactMediaLibrary;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ReactMediaLibraryTest
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs\Controller\ReactMediaLibrary
 */
class ReactMediaLibraryTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'field',
    'react_paragraphs',
    'media',
    'media_library',
    'views',
    'user',
    'image',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('media');
    $this->installEntitySchema('user_role');
    $this->installEntitySchema('view');
    $this->installEntitySchema('image_style');
    $this->installConfig('media_library');

    $current_request = new Request([
      'media_library_opener_id' => 'media_library.opener.field_widget',
      'media_library_allowed_types' => ['image'],
      'media_library_selected_type' => 'image',
      'media_library_remaining' => 1,
      'media_library_opener_parameters' => [
        'field_widget_id' => '',
        'entity_type_id' => 'paragraph',
        'bundle' => '',
        'field_name' => '',
      ],
    ]);

    $request_stack = $this->createMock(RequestStack::class);
    $request_stack->method('getCurrentRequest')->willReturn($current_request);
    \Drupal::getContainer()->set('request_stack', $request_stack);
  }

  /**
   * Test the controller response.
   */
  public function testController() {
    $controller = ReactMediaLibrary::create(\Drupal::getContainer());
    $response = $controller->mediaLibrary();
    $commands = $response->getCommands();
    $this->assertCount(1, $commands);
    $this->assertEquals('openDialog', $commands[0]['command']);
  }

}
