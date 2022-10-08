<?php

namespace Drupal\my_custom_action;


// use Drupal\node\Entity\Node;
// use Drupal\taxonomy\Entity\Term;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\ProductVariation;
use \Drupal\commerce_store\Entity\Store;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_order\Entity\Order;

class CsvMigrateSubscriptionOrders {

  public static function MigrateSubscriptionOrder($item, &$context) {
    $context['sandbox']['current_item'] = $item;
    $message = 'Migrating ' . $item['title'];
    $results = array();
    create_subscription_order($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  public static function MigrateSubscriptionOrderCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        '1 License migrated.', '@count Licenses migrated.'
      );
    } else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }
}


function create_subscription_order($csv_row) {
  // $user = \Drupal::currentUser();
  // echo '<pre>';print_r($csv_row);echo '</pre>';
  // exit;
  $csc_product_variation_column = $csv_row['service_name'];
  switch($csc_product_variation_column) {
    case 'Already a Print Subscriber?':
    break;
    case 'Online Only Subscription':
      if($csv_row['price'] == 84 && $csv_row['duration'] == 360) {
        $product_variation_id = 162;
      } else if($csv_row['price'] == 42 && $csv_row['duration'] == 180) {
        $product_variation_id = 161;
      } else if($csv_row['price'] == 21 && $csv_row['duration'] == 90) {
        $product_variation_id = 160;
      } else if($csv_row['price'] == 7 && $csv_row['duration'] == 30) {
        $product_variation_id = 163;
      } else if($csv_row['price'] == 1 && $csv_row['duration'] == 1) {
        $product_variation_id = 159;
      }
    break;
    case 'Online Only Subscription -- Auto Renew':
      $product_variation_id = 163;
    break;
    case 'Free 30-Day Trial':
      $product_variation_id = 163;
    break;
  }
  if($product_variation_id) {
    $uid = '';
    $email_value = '';
    $email_value = $csv_row['user_email'];
    $database = \Drupal::database();
    $query = $database->select('users_field_data', 'u');
    $query->condition('u.mail', $email_value);
    $query->fields('u', ['uid']);
    $result = $query->execute()->fetchAll();
    $uid = $result[0]->uid;
    $entity_manager = \Drupal::entityManager();
    $product_variation = $entity_manager->getStorage('commerce_product_variation')->load($product_variation_id);
    $license_type_plugin = $product_variation->get('license_type')->first()->getTargetInstance();
    $license = \Drupal::entityTypeManager()->getStorage('commerce_license')->create([
      'type' => $license_type_plugin->getPluginId(),
      'state' => 'active',
      'product_variation' => $product_variation->id(),
      'uid' => $uid,
      // Take the expiration type configuration from the product variation expiration field.
      'granted' => strtotime($csv_row['startTime']),
      'expires' => strtotime($csv_row['expireTime']),
      'expiration_type' => $product_variation->license_expiration,
    ]);

    // Set the license's plugin-specific configuration from the product variation's license_type field plugin instance.
    $license->setValuesFromPlugin($license_type_plugin);
    $license->save();
    \Drupal::state()->set('my_custom_action.migration_subscription_csv_id_and_license_id_'.$csv_row['id'], $license->id());
    // if($csc_product_variation_column == 'Online Only Subscription') {
    //   /**
    //    * Create Order item
    //    */
    //   $order_item = OrderItem::create([
    //     'type' => 'default',
    //     'purchased_entity' => $product_variation,
    //     'quantity' => 1,
    //     // Omit these lines to preserve original product price.
    //     'unit_price' => $product_variation->getPrice(),
    //     // 'overridden_unit_price' => TRUE,
    //     'title' => $product_variation->label(),
    //     'license' => $license->id(),
    //   ]);
    //   $order_item->save();
    //   /**
    //    * Create Order
    //    */
    //   $order = \Drupal\commerce_order\Entity\Order::create([
    //     'type' => 'default',
    //     'state' => 'fulfillment',
    //     'mail' => $email_value,
    //     'uid' => $uid,
    //     'ip_address' => '',
    //     // 'order_number' => '6',
    //     // 'billing_profile' => '',
    //     'store_id' => 11,
    //     'order_items' => [$order_item],
    //     'placed' => strtotime($csv_row['startTime']),
    //   ]);
    //   $order->save();
    //   $order_type_storage = \Drupal::entityTypeManager()->getStorage('commerce_order_type');
    //   $order_type = $order_type_storage->load($order->bundle());
    //   /** @var \Drupal\commerce_number_pattern\Entity\NumberPatternInterface $number_pattern */
    //   $number_pattern = $order_type->getNumberPattern();
    //   if ($number_pattern) {
    //     $order_number = $number_pattern->getPlugin()->generate($order);
    //   } else {
    //     $order_number = $order->id();
    //   }
    //   $order->setOrderNumber($order_number);
    //   $order->save();
    // }
  }
}
