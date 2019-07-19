<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Template\Attribute;
use Drupal\entity_reference_revisions\Plugin\Field\FieldFormatter\EntityReferenceRevisionsEntityFormatter;

/**
 * Plugin implementation of the 'react_paragraphs' formatter.
 *
 * @FieldFormatter(
 *   id = "react_paragraphs",
 *   label = @Translation("React Paragraphs"),
 *   field_types = {
 *     "react_paragraphs"
 *   }
 * )
 */
class ReactParagraphs extends EntityReferenceRevisionsEntityFormatter {

  /**
   * {@inheritDoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    $organized_elements = [];

    foreach ($items->getValue() as $delta => $item) {
      if (empty($item['settings'])) {
        $item['settings'] = json_encode([
          'width' => 12,
          'row' => $delta,
          'index' => 0,
        ]);
      }
      $settings = json_decode($item['settings'], TRUE);

      foreach ($elements as $key => $element) {
        if ($item['target_id'] == $element['#paragraph']->id()) {
          unset($elements[$key]);

          $organized_elements[$settings['row']]['attributes'] = new Attribute(['class' => ['react-paragraphs-row']]);
          $organized_elements[$settings['row']]['items'][$settings['index']] = [
            'entity' => $element,
            'width' => $settings['width'],
            'attributes' => new Attribute(['class' => ['react-item'], 'data-react-columns' => $settings['width']]),
          ];

          if (!isset($organized_elements[$settings['row']]['width'])) {
            $organized_elements[$settings['row']]['width'] = 0;
          }
          $organized_elements[$settings['row']]['width'] += $settings['width'];
        }
      }
    }

    ksort($organized_elements);
    array_walk($organized_elements, 'ksort');

    $this->addSpacers($organized_elements);

    return [
      [
        '#theme' => 'react_paragraphs',
        '#rows' => $organized_elements,
      ],
      '#attached' => ['library' => ['react_paragraphs/field_formatter']],
    ];
  }

  /**
   * Add spacers to the end of rows.
   *
   * @param array $elements
   *   Organized field items.
   */
  protected function addSpacers(array &$elements) {
    foreach ($elements as &$row) {
      if ($row['width'] < 12) {
        $row[] = [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => ['data-react-columns' => 12 - $row['width']],
        ];
      }

      unset($row['width']);
    }
  }

}
