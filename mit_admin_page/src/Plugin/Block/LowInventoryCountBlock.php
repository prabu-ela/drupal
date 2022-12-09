<?php

namespace Drupal\mit_admin_page\Plugin\Block;

use Drupal\views\Views;
use Drupal\node\NodeInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\ProductAttributeValue;

/**
 * Provides a 'Low Inventory' block.
 *
 * @Block(
 *   id = "low_inventory_block",
 *   admin_label = @Translation("Low Inventory Block")
 * )
 */
class LowInventoryCountBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get and loop through the View `low_inventory_count`.
    $view = Views::getView('low_inventory_count');
    $view->setDisplay('block_1');
    $view->execute();
  
    // Get the results of the view.
    $view_result = $view->result;

    // Calling Stock level service.
    $stockServiceManager = \Drupal::service('commerce_stock.service_manager');

    foreach ($view_result as $key => $value) {
      $data['low_inventory'] = $value->_relationship_entities['reverse__commerce_product_variation__product_id']->field_low_inventory_number->value;

      // Loading inventory details.
      $product_variation = ProductVariation::load($value->commerce_product_variation_field_data_commerce_product_field);
      $data['instock'] = intval($stockServiceManager->getStockLevel($product_variation));

      // Fetching only low inventory title.
      if ($data['low_inventory'] > $data['instock'] ) {
        $result[$value->product_id][$value->product_id] = $value->_entity->field_ticket->entity->title->value;
      }
    }

    $count = count($result);
    $display_date = [];

    if ($count > 1) {
      $display_data['first'] = array_slice($result, 0, 3);
      $display_data['last'] = array_slice($result, 3, $count);
    }

    $renderable = [
      '#theme' => 'mitac_lowinventory_count',
      '#default_show' => empty($display_data['first']) ? $result : $display_data['first'],
      '#readmore_show' => empty($display_data['last']) ? NULL : $display_data['last'],
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return $renderable;
  }
}
