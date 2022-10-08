<?php

namespace Drupal\nlr_roles_permissions\Plugin\Block;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'article' block.
 *
 * @Block(
 *   id = "roman_block",
 *   admin_label = @Translation("Roman block"),
 *   category = @Translation("Custom Roman block")
 * )
 */
class RomanBlock extends BlockBase {

  /**
   * Roman Number for article details page.
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');

    // Cheking current page instance of node.
    if ($node instanceof NodeInterface) {
      $nid = $node->id();
      $node_data = Node::load($nid);
      $node_created_date = $node_data->getCreatedTime();

      $data = $this->getRoman($node_created_date);
      $value = 'National Law Review, Volumess ' . $data . ', Number ' . (date("z", $node_created_date) + 1);
      return [
        '#markup' => $value,
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * Helper Funtion to get Roman Number.
   */
  public function getRoman($date) {
    $number = (date("Y", $date) - 2010);
    $map = [
      'M' => 1000,
      'CM' => 900,
      'D' => 500,
      'CD' => 400,
      'C' => 100,
      'XC' => 90,
      'L' => 50,
      'XL' => 40,
      'X' => 10,
      'IX' => 9,
      'V' => 5,
      'IV' => 4,
      'I' => 1,
    ];
    $returnValue = '';
    while ($number > 0) {
      foreach ($map as $roman => $int) {
        if ($number >= $int) {
          $number -= $int;
          $returnValue .= $roman;
          break;
        }
      }
    }
    return $returnValue;
  }

}
