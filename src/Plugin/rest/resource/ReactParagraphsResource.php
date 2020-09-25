<?php

namespace Drupal\react_paragraphs\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Render\Element;
use Drupal\field\FieldConfigInterface;
use Drupal\react_paragraphs\ReactParagraphsFieldsManager;
use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "react_paragraphs",
 *   label = @Translation("React Paragraphs"),
 *   uri_paths = {
 *     "canonical" = "/api/react-paragraphs/{entity_type_id}/{bundle}"
 *   }
 * )
 */
class ReactParagraphsResource extends ResourceBase {

  /**
   * Entity Type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Module handler server.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Form builder service.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $formBuilder;

  /**
   * React field plugin manager service.
   *
   * @var \Drupal\react_paragraphs\ReactParagraphsFieldsManager
   */
  protected $reactFieldsPluginManager;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('entity_type.manager'),
      $container->get('entity.form_builder'),
      $container->get('plugin.manager.react_paragraphs_fields'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, EntityTypeManagerInterface $entity_type_manager, EntityFormBuilderInterface $form_builder, ReactParagraphsFieldsManager $field_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->entityTypeManager = $entity_type_manager;
    $this->formBuilder = $form_builder;
    $this->reactFieldsPluginManager = $field_manager;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritDoc}
   */
  public function permissions() {
    return [];
  }

  /**
   * The $_GET response to return data for an entity bundle form.
   *
   * @param string $entity_type_id
   *   Entity type id.
   * @param string $bundle
   *   Entity bundle id.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Json data structured for the react widget.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function get($entity_type_id, $bundle) {

    $data = [];
    $entity_type_definition = $this->entityTypeManager->getDefinition($entity_type_id);
    $bundle_key = $entity_type_definition->getKey('bundle');
    $empty_entity = $this->entityTypeManager->getStorage($entity_type_id)
      ->create([$bundle_key => $bundle]);

    // Verify the user has access to edit an entity of this type.
    if (!$empty_entity->access('edit')) {
      return new JsonResponse('Permission denied.', 401);
    }
    $form = $this->formBuilder->getForm($empty_entity);

    // Loop through the various fields on the form to build the json data.
    $field_config_storage = $this->entityTypeManager->getStorage('field_config');
    foreach (Element::children($form) as $field_name) {
      if (!isset($form[$field_name]['widget'])) {
        continue;
      }

      // Find the plugin for the current field in the form and get the data
      // that can be used by the react widget.
      $field_config = $field_config_storage->load("$entity_type_id.$bundle.$field_name");
      if ($field_config && ($plugin = $this->getReactFieldsPlugin($field_config))) {
        $data['form'][$field_name] = $plugin->getFieldInfo($form[$field_name], $field_config);
        $this->moduleHandler->alter(
          'react_paragraphs_form_field_data',
          $data['form'][$field_name],
          $form[$field_name],
          $field_config
        );
      }
    }

    uasort($data['form'], [
      '\Drupal\Component\Utility\SortArray',
      'sortByWeightElement',
    ]);

    if (method_exists($empty_entity, 'getParagraphType')) {
      $paragraphs_type = $empty_entity->getParagraphType();
      if ($paragraphs_type && \Drupal::currentUser()
          ->hasPermission('edit behavior plugin settings')) {
        foreach ($paragraphs_type->getEnabledBehaviorPlugins() as $plugin_id => $plugin) {
          $data['behavior_plugins'][$plugin_id] = [];
          if ($plugin_form = $plugin->buildBehaviorForm($empty_entity, $data['behavior_plugins'][$plugin_id], new FormState())) {
            $data['behavior_plugins'][$plugin_id] = $plugin_form;
          }
        }
      }
    }
    return new JsonResponse($data);
  }

  /**
   * Find an applicable react field plugin that matches the field config.
   *
   * @param \Drupal\field\FieldConfigInterface $field_config
   *   Field config entity.
   *
   * @return \Drupal\react_paragraphs\ReactParagraphsFieldsInterface|null
   *   The plugin that is for the field.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function getReactFieldsPlugin(FieldConfigInterface $field_config) {
    foreach ($this->reactFieldsPluginManager->getDefinitions() as $plugin_definition) {
      if (in_array($field_config->getType(), $plugin_definition['field_types'])) {
        return $this->reactFieldsPluginManager->createInstance($plugin_definition['id']);
      }
    }
    return $this->reactFieldsPluginManager->createInstance('default');
  }

}
