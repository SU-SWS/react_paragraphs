<?php

namespace Drupal\react_paragraphs\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Template\Attribute;
use Drupal\entity_reference_revisions\Plugin\Field\FieldFormatter\EntityReferenceRevisionsEntityFormatter;
use Drupal\paragraphs\ParagraphInterface;

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

    // The elements are a single column list of paragraph entities provided by
    // entity_reference_revisions. We want to organize them into row groups
    // and put them in the correct order.
    foreach ($elements as $delta => $element) {
      $item = $this->getItem($element['#paragraph'], $items);

      // In an unusual case that the settings is not valid data, we'll populate
      // it with some basic values.
      if (empty($item['settings'])) {
        $item['settings'] = [
          'width' => 12,
          'row' => $delta,
          'index' => 0,
        ];
      }

      // Always add attributes to the existing row.
      $organized_elements[$item['settings']['row']]['attributes'] = new Attribute(['class' => ['react-paragraphs-row']]);

      $bundle = $elements[$delta]['#paragraph']->bundle();

      // Add the paragraph render array to the list of items in the correct row.
      $organized_elements[$item['settings']['row']]['items'][$item['settings']['index']] = [
        'entity' => $elements[$delta],
        'width' => $item['settings']['width'],
        'attributes' => new Attribute([
          'class' => [
            'paragraph-item',
            Html::cleanCssIdentifier("ptype-$bundle"),
          ],
          'data-react-columns' => $item['settings']['width'],
        ]),
      ];

      // Add up the width of all elements with each row to provide a spacer in
      // the later steps.
      if (!isset($organized_elements[$item['settings']['row']]['width'])) {
        $organized_elements[$item['settings']['row']]['width'] = 0;
      }
      $organized_elements[$item['settings']['row']]['width'] += $item['settings']['width'];
    }

    // Ensure we have all the rows and items sorted correctly based on their row
    // and item indexes.
    ksort($organized_elements);
    array_walk($organized_elements, 'ksort');

    // Add any spacers to the end of the rows.
    $this->addSpacers($organized_elements);

    // Return a field render array. Set `#is_multiple` to false to reduce
    // unnecessary markup since the included template handles the multiple.
    return [
      ['#theme' => 'react_paragraphs', '#rows' => $organized_elements],
      '#is_multiple' => FALSE,
      '#attached' => ['library' => ['react_paragraphs/field_formatter']],
    ];
  }

  /**
   * Get the item from the list that matches a paragraph entity.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $entity
   *   Content entity being rendered.
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   Field item list from the current entity.
   *
   * @return array
   *   Item value that matches the entity.
   */
  protected function getItem(ParagraphInterface $entity, FieldItemListInterface $items) {
    foreach ($items->getValue() as $item) {
      if ($item['target_id'] == $entity->id()) {
        return $item;
      }
    }
  }

  /**
   * Add spacers to the end of rows.
   *
   * @param array $elements
   *   Organized field items.
   */
  protected function addSpacers(array &$elements) {
    foreach ($elements as &$row) {
      // When the rows aren't 12 columns wide, add a last element with the
      // remaining columns.
      if ($row['width'] < 12) {
        $row['items'][] = [
          'entity' => [
            '#type' => 'html_tag',
            '#tag' => 'div',
          ],
          'attributes' => new Attribute(['data-react-columns' => 12 - $row['width']]),
        ];
      }
      // Cleanup the row.
      unset($row['width']);
    }
  }

}
