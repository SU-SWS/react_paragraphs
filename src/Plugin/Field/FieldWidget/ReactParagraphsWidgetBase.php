<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\editor\Plugin\EditorManager;
use Drupal\field\FieldConfigInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Plugin\EntityReferenceSelection\ParagraphSelection;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ReactParagraphsWidgetBase extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Selection plugin manager service.
   *
   * @var \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface
   */
  protected $selectionManager;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Editor manager service.
   *
   * @var \Drupal\editor\Plugin\EditorManager
   */
  protected $editorManager;

  /**
   * Current account object.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('plugin.manager.entity_reference_selection'),
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.editor'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, SelectionPluginManagerInterface $selection_manager, EntityTypeManagerInterface $entity_type_manager, EditorManager $editor_manager, AccountProxyInterface $current_user) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->selectionManager = $selection_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->editorManager = $editor_manager;
    $this->currentUser = $current_user;
  }

}
