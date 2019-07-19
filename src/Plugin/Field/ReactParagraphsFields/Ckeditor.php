<?php

namespace Drupal\react_paragraphs\Plugin\Field\ReactParagraphsFields;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\filter\Entity\FilterFormat;
use Drupal\react_paragraphs\ReactParagraphsFieldsBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Text
 *
 * @ReactParagraphsFields(
 *   id = "ckeditor",
 *   field_types = {
 *     "text_long",
 *     "text_with_summary"
 *   }
 * )
 */
class Ckeditor extends ReactParagraphsFieldsBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritDoc}
   */
  public function getFieldInfo(array $field_element, FieldConfigInterface $field_config) {
    $info = parent::getFieldInfo($field_element, $field_config);

    $info['allowed_formats'] = filter_formats($this->currentUser);
    $info['summary'] = $field_config->getSetting('display_summary');
    $info['summary'] = is_null($info['summary']) ? FALSE : TRUE;

    array_walk($info['allowed_formats'], function (FilterFormat &$format) {
      $format = $format->label();
    });

    $allowed_formats = $field_config->getThirdPartySettings('allowed_formats');
    if (is_array($allowed_formats) && !empty(array_filter($allowed_formats))) {
      $info['allowed_formats'] = array_intersect_key($info['allowed_formats'], array_filter($allowed_formats));
    }
    return $info;
  }

}
