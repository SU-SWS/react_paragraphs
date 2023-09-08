<?php

namespace Drupal\react_paragraphs\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormState;
use Drupal\media_library\MediaLibraryState;
use Drupal\media_library\Plugin\Field\FieldWidget\MediaLibraryWidget;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ReactMediaLibrary.
 *
 * @package Drupal\react_paragraphs\Controller
 */
class ReactMediaLibrary extends ControllerBase {

  /**
   * @var \Symfony\Component\HttpFoundation\Request|null
   */
  protected $request;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('request_stack'));
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(RequestStack $request_stack) {
    $this->request = $request_stack->getCurrentRequest();
  }

  /**
   * Get the media library builder response.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response.
   */
  public function mediaLibrary() {
    // Prevent the response to be a cached version of the view.
    // Adjust this when the view can be different.
    // @link https://www.drupal.org/project/drupal/issues/2971209
    Cache::invalidateTags(['config:views.view.media_library']);
    $form_state = new FormState();
    $query = $this->request->query;

    $media_state = MediaLibraryState::create(
      $query->get('media_library_opener_id'),
      json_decode($query->get('media_library_allowed_types', ''), TRUE),
      $query->get('media_library_selected_type'),
      $query->get('media_library_remaining'),
      json_decode($query->get('media_library_opener_parameters', ''), TRUE)
    );

    $form_state->setTriggeringElement(['#media_library_state' => $media_state]);
    return MediaLibraryWidget::openMediaLibrary([], $form_state);
  }

}
