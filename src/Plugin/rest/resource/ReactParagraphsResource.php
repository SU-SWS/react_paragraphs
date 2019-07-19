<?php

namespace Drupal\react_paragraphs\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
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
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $formBuilder;

  /**
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
      $container->get('plugin.manager.react_paragraphs_fields')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, EntityTypeManagerInterface $entity_type_manager, EntityFormBuilderInterface $form_builder, ReactParagraphsFieldsManager $field_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->entityTypeManager = $entity_type_manager;
    $this->formBuilder = $form_builder;
    $this->reactFieldsPluginManager = $field_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function permissions() {
    return [];
  }

  public function get($entity_type_id, $bundle) {
    $data = [];
    $entity_type_definition = $this->entityTypeManager->getDefinition($entity_type_id);
    $bundle_key = $entity_type_definition->getKey('bundle');
    $empty_entity = $this->entityTypeManager->getStorage($entity_type_id)
      ->create([$bundle_key => $bundle]);

    if (!$empty_entity->access('edit')) {
      return new JsonResponse('Permission denied.', 401);
    }
    $form = $this->formBuilder->getForm($empty_entity);

    $field_config_storage = $this->entityTypeManager->getStorage('field_config');
    foreach (Element::children($form) as $field_name) {
      if (!isset($form[$field_name]['widget'])) {
        continue;
      }

      $field_config = $field_config_storage->load("$entity_type_id.$bundle.$field_name");
      if ($field_config && ($plugin = $this->getReactFieldsPlugin($field_config))) {
        $data[$field_name] = $plugin->getFieldInfo($form[$field_name], $field_config);
      }
    }

    uasort($data, [
      '\Drupal\Component\Utility\SortArray',
      'sortByWeightElement',
    ]);
    return new JsonResponse($data);
  }

  /**
   * @param \Drupal\field\FieldConfigInterface $field_config
   *
   * @return \Drupal\react_paragraphs\ReactParagraphsFieldsInterface|null
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function getReactFieldsPlugin(FieldConfigInterface $field_config) {
    foreach ($this->reactFieldsPluginManager->getDefinitions() as $plugin_definition) {
      if (in_array($field_config->getType(), $plugin_definition['field_types'])) {
        return $this->reactFieldsPluginManager->createInstance($plugin_definition['id']);
      }
    }
  }

}
