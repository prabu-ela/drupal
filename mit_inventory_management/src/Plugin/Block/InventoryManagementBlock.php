<?php

namespace Drupal\mit_inventory_management\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Inventory Management' Block.
 *
 * @Block(
 *   id = "inventory_management_block",
 *   admin_label = @Translation("Inventory Management Block"),
 *   category = @Translation("Custom"),
 * )
 */
class InventoryManagementBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $content = [];

    $pager = 0;
    if (!empty(\Drupal::request()->get('pager'))) {
      $pager = \Drupal::request()->get('pager');
    }

    $evtParams = \Drupal::request()->get('event_title');
    $evtEmptyRemoved = [];
    if (!empty($evtParams) && is_array($evtParams)) {
      $evtEmptyRemoved = array_filter($evtParams);
    }

    $locParams = \Drupal::request()->get('stock_location');
    $locEmptyRemoved = [];
    if (!empty($locParams) && is_array($locParams)) {
      $locEmptyRemoved = array_filter($locParams);
    }

    $vendorParams = \Drupal::request()->get('vendor_title');
    $vendorEmptyRemoved = [];
    if (!empty($vendorParams) && is_array($vendorParams)) {
      $vendorEmptyRemoved = array_filter($vendorParams);
    }

    $transTypeParams = \Drupal::request()->get('transaction_type');
    $transTypeEmptyRemoved = [];
    if (!empty($transTypeParams) && is_array($transTypeParams)) {
      $transTypeEmptyRemoved = array_filter($transTypeParams);
    }

    $transQry = \Drupal::database()->select('inventory_transaction_type', 'itt');
    $transQry->extend('\Drupal\Core\Database\Query\PagerSelectExtender');
    $transQry->join('node_field_data', 'nfd', 'nfd.nid = itt.event_id');
    $transQry->join('commerce_stock_location_field_data', 'csl', 'csl.location_id = itt.location');
    $transQry->join('commerce_product_field_data', 'cpd', 'cpd.product_id = itt.ticket_id');
    $transQry->fields('itt', ['event_id', 'quantity', 'unit_price', 'purchase_date', 'created', 'transaction_type', 'ticket_expiry_date']);
    $transQry->fields('nfd', ['title', 'created']);
    $transQry->fields('csl', ['name', 'location_id']);
    $transQry->fields('cpd', ['title', 'product_id']);

    if (!empty($evtEmptyRemoved)) {
      $transQry->condition('itt.event_id', $evtEmptyRemoved, 'IN');
    }

    if (!empty($locEmptyRemoved)) {
      $transQry->condition('itt.location', $locEmptyRemoved, 'IN');
    }

    if (!empty($vendorEmptyRemoved)) {
      $transQry->condition('itt.vendor_id', $vendorEmptyRemoved, 'IN');
    }

    if (!empty($transTypeEmptyRemoved)) {
      $transQry->condition('itt.transaction_type', $transTypeEmptyRemoved, 'IN');
    }

    $pager = $transQry->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(50);
    $inventoryTrans = $pager->execute()->fetchAll();
    
    if (!empty($inventoryTrans)) {
      $content['events'] = [];
      $trasactionTypes = [
        'receiveStock' => 'Receive stock',
        'moveStock' => 'Move stock'
      ];
      foreach ($inventoryTrans as $key => $singleTrans) {
        if (!isset($content['events'][$singleTrans->event_id])) {
          $content['events'][$singleTrans->event_id]['title'] = $singleTrans->title;
          $content['events'][$singleTrans->event_id]['created'] = date('D m/d/y H:iA', $singleTrans->nfd_created);
          $content['events'][$singleTrans->event_id]['options'] = [];
        }

        $content['events'][$singleTrans->event_id]['options'][$singleTrans->product_id . '_' . $singleTrans->location_id] = [
          'title' => $singleTrans->cpd_title,
          'location' => $singleTrans->name,
          'mitac_price' => NULL,
          'retail_price' => NULL,
          'mit_price' => NULL,
          'purchase_date' => !empty($singleTrans->purchase_date) ? date('D m/d/y', strtotime($singleTrans->purchase_date)) : NULL,
          'transaction_time' => !empty($singleTrans->created) ? date('D m/d/y H:iA', strtotime($singleTrans->created)) : NULL,
          'transaction_type' => isset($trasactionTypes[$singleTrans->transaction_type]) ? $trasactionTypes[$singleTrans->transaction_type] : NULL,
          'ticket_expiration_date' => !empty($singleTrans->ticket_expiry_date) ? date('D m/d/y', strtotime($singleTrans->ticket_expiry_date)) : NULL
        ];

        $prodctLocQty = $singleTrans->quantity;
        $evntProdctLocQty = 0;
        // $evntProdctLocQty = $singleTrans->quantity;

        if (isset($content['events'][$singleTrans->event_id]['options'][$singleTrans->product_id . '_' . $singleTrans->location_id]['quantity'])) {

          $prodctLocPrevQty = $content['events'][$singleTrans->event_id]['options'][$singleTrans->product_id . '_' . $singleTrans->location_id]['quantity'];
          
          if (strpos($singleTrans->quantity, '-') !== false) {
            // $newQty = trim($singleTrans->quantity, '-');
            // $newQty = (int) $newQty;
            // $prodctLocQty = $prodctLocPrevQty - $newQty;
          }
          else {
            $newQty = (int) $singleTrans->quantity;
            $prodctLocQty = $prodctLocPrevQty + $newQty;
          }
        }

        if (isset($content['events'][$singleTrans->event_id]['quantity'])) {

          $eventProdctLocPrevQty = $content['events'][$singleTrans->event_id]['quantity'];
          
          if (strpos($prodctLocQty, '-') !== false) {
            // $newQty = trim($prodctLocQty, '-');
            // $newQty = (int) $newQty;
            // $evntProdctLocQty = $eventProdctLocPrevQty - $newQty;
          }
          else {
            $newQty = (int) $prodctLocQty;
            $evntProdctLocQty = $eventProdctLocPrevQty + $newQty;
          }
        }
        
        $content['events'][$singleTrans->event_id]['options'][$singleTrans->product_id . '_' . $singleTrans->location_id]['quantity'] = $prodctLocQty;
        $content['events'][$singleTrans->event_id]['quantity'] = $evntProdctLocQty;
      }
    }

    return [
      '#cache' => ['max-age' => 0],
      '#theme' => 'inventory_management_block',
      '#query_params' => '',
      '#pager' => $pager,
      '#content' => $content,
    ];
  }

}
