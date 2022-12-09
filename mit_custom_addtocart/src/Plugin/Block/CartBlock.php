<?php

namespace Drupal\mit_custom_addtocart\Plugin\Block;

use Drupal\views\Views;
use Drupal\node\NodeInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\ProductAttributeValue;

/**
 * Provides a 'Custom Cart' block.
 *
 * @Block(
 *   id = "custom_vart_block",
 *   admin_label = @Translation("Custom Cart")
 * )
 */
class CartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      // You can get nid and anything else you need from the node object.
      $nid = $node->id();

      // Get and loop through the View `product_id_add_to_cart`.
      $view = Views::getView('product_id_add_to_cart');
      $arg = [$nid];
      $view->setArguments($arg);
      $view->setDisplay('block_1');
      $view->execute();

      // Get the results of the view.
      $view_result = $view->result;

      $stockServiceManager = \Drupal::service('commerce_stock.service_manager');

      $i = 0;
      foreach ($view_result as $key => $value) {
        $data['product'] = $value->product_id;
        $attirbute = ProductAttributeValue::load($value->_relationship_entities['variations_target_id']->field_seating_type->target_id);
        foreach ($attirbute as $val) {

          // Getting stock level.
          $stock_level = 0;
          $product_variation = ProductVariation::load($value->commerce_product_variation_field_data_commerce_product__vari);

          $stock_level = intval($stockServiceManager->getStockLevel($product_variation));

          // Checking expiriation date of variation.
          if (isset($value->_relationship_entities['variations_target_id']->field_expiration_date->value)) {
            $expiration_date = $value->_relationship_entities['variations_target_id']->field_expiration_date->value;
            $old_date = strtotime($expiration_date);
            $today_date = time();
            if ($today_date > $old_date) {
              $data[$value->commerce_product_variation_field_data_commerce_product__vari][$attirbute->name->value]['available'] = 'Not Available';
            }
          }

          // Building the array with stock level more then 0.
          $data[$value->commerce_product_variation_field_data_commerce_product__vari][$attirbute->name->value]['varition'] = $value->commerce_product_variation_field_data_commerce_product__vari;
          $data[$value->commerce_product_variation_field_data_commerce_product__vari][$attirbute->name->value]['stock'] = $stock_level;
          $data[$value->commerce_product_variation_field_data_commerce_product__vari][$attirbute->name->value]['price'] = $value->_relationship_entities['variations_target_id']->getPrice()->getNumber();
          $data[$value->commerce_product_variation_field_data_commerce_product__vari][$attirbute->name->value]['limit'] = $product_variation->field_ticket_purchase_limit->value;
        }
      }
    }

    $renderable = [
      '#theme' => 'mitac_addtocart',
      '#data' => $data,
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return $renderable;
  }

}
