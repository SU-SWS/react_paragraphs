<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\field\FieldConfigInterface;
use Drupal\filter\FilterFormatInterface;
use Drupal\filter\FilterFormatRepositoryInterface;
use Drupal\react_paragraphs\Attribute\ReactParagraphsFields;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Ckeditor field plugin.
 */
#[ReactParagraphsFields(
  id: 'ckeditor',
  field_types: ['text_long', 'text_with_summary'],
)]
class Ckeditor extends ReactParagraphsFieldsBase {

  /**
   * Current user object.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Filter format repository.
   *
   * @var \Drupal\filter\FilterFormatRepositoryInterface
   */
  protected $filterFormatRepository;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->currentUser = $container->get('current_user');
    $instance->filterFormatRepository = $container->get(FilterFormatRepositoryInterface::class);
    return $instance;
  }

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $info = parent::getFieldInfo($field_element, $field_config);

    $info['allowed_formats'] = $this->getFilterFormats();
    $info['summary'] = $field_config->getSetting('display_summary');
    $info['summary'] = is_null($info['summary']) ? FALSE : TRUE;

    array_walk($info['allowed_formats'], function (FilterFormatInterface &$format) {
      $format = $format->label();
    });

    $allowed_formats = $field_config->getThirdPartySettings('allowed_formats');
    if (is_array($allowed_formats) && !empty(array_filter($allowed_formats))) {
      $info['allowed_formats'] = array_intersect_key($info['allowed_formats'], array_filter($allowed_formats));
    }
    return $info;
  }

  /**
   * Get all filter formats allowed for the current user.
   *
   * @return \Drupal\filter\FilterFormatInterface[]
   *   Keyed array of filter formats.
   */
  protected function getFilterFormats() {
    return $this->filterFormatRepository->getFormatsForAccount($this->currentUser);
  }

}
