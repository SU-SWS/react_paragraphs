<?php

namespace Drupal\react_paragraphs;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Service Provider for Entity Reference Revisions.
 */
class ReactParagraphsServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $modules = $container->getParameter('container.modules');
    if (isset($modules['hal'])) {
      // Hal module is enabled, add our new normalizer for react paragraphs
      // items.
      $service_definition = new Definition('Drupal\react_paragraphs\Normalizer\ReactParagraphsItemNormalizer', [
        new Reference('hal.link_manager'),
        new Reference('serializer.entity_resolver'),
      ]);
      // The priority must be higher than that of
      // serializer.normalizer.entity_reference_revision_item.
      // @see \Drupal\entity_reference_revisions\EntityReferenceRevisionsServiceProvider::alter()
      $service_definition->addTag('normalizer', ['priority' => 25]);
      $container->setDefinition('react_paragraphs.normalizer.react_paragraphs_item.hal', $service_definition);
    }
  }

}
