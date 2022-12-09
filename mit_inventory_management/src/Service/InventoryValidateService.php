<?php

namespace Drupal\mit_inventory_management\Service;

use Drupal\views\Views;

/**
 * Class for the inventory management.
 */
class InventoryValidateService {

  /**
   * Validating the inventory passing variation id.
   *
   * @param int $vid
   *   The Product Variation ID.
   */
  public function validateInventory($vid = []) {

    // Checking Variation id is empty or not.
    if (!empty($vid)) {

      // Get and loop through the View `group_members_per_group`.
      $view = Views::getView('inventory_validation');
      $arg = [$vid];
      $view->setArguments($arg);
      $view->setDisplay('block_1');
      $view->execute();

      // Get the results of the view.
      $view_result = $view->result;

      // Looping through the view result & ordering array.
      foreach ($view_result as $value) {
        if (!empty($value->inventory_transaction_type_quantity)) {
          $result[$value->inventory_transaction_type_location]['location_id'] = $value->inventory_transaction_type_location;
          $result[$value->inventory_transaction_type_location]['inventory'] = $value->inventory_transaction_type_quantity;
          $result[$value->inventory_transaction_type_location]['nid'] = $value->node_field_data_inventory_transaction_type_nid;
        }
      }
      return $result;
    }
  }

}
