<?php

namespace Drupal\my_custom_action\Commands;

use Drush\Commands\DrushCommands;

/**
 * Need to execute drupal automated cron through drush commmand
 */
class AdvanceSubscriptionDrush extends DrushCommands {

  /**
   * @command AdvanceSubscriptionDrush:drushCreditCardExpiryMailCron
   * @aliases drush-credit-card-expiry-mail-cron
   */
  public function drushCreditCardExpiryMailCron() {
    
    if(date('j') == 1) {
        $current_month = date('m');
        // add month to current month to fetch credit card which is going to expry next month
        $credit_card_next_month = $current_month + 1;
        $credit_card_year = date('Y');
        if($current_month == 12) {
          $credit_card_next_month = 1;
          $credit_card_year = date('Y') + 1;
        }
        $db = \Drupal::database();
        $query = $db->select('commerce_subscription', 'cs');
        $query->join('commerce_payment_method', 'cpm', 'cs.payment_method = cpm.method_id');
        // $query->join('commerce_payment', 'cp', 'cp.payment_method = cpm.method_id');
        $query->join('commerce_payment_method__card_exp_month', 'ccm', 'cpm.method_id = ccm.entity_id and ccm.bundle = :bundle', [':bundle' => 'credit_card']);
        // $query->join('commerce_order', 'co', 'co.order_id = cp.order_id');
        // $query->join('commerce_order_item', 'coi', 'coi.order_id = cp.order_id');
        // $query->join('commerce_product_variation', 'cpv', 'coi.purchased_entity = cpv.variation_id');
        $query->fields('cpm', ['uid']);
        $query->join('commerce_payment_method__card_exp_year', 'ccy', 'cpm.method_id = ccy.entity_id and ccy.bundle = :bundle', [':bundle' => 'credit_card']);
        $query->condition('cpm.payment_gateway', 'auto_renewal_subscriptions');
        $query->condition('cpm.type', 'credit_card');
        $query->condition('ccm.card_exp_month_value', $credit_card_next_month);
        $query->condition('ccy.card_exp_year_value', $credit_card_year);
        $query->condition('cpm.uid', 0, '<>');
        $query->condition('cs.state', 'active');
        // $query->condition('cpv.type', ['default', 'free_trial_to_rolling'], 'IN');
        $credit_card_query_result = $query->execute()->fetchAllKeyed(0, 0);
        $credit_card_query_result = [];
        $uids = array_chunk($credit_card_query_result, 3);
        if(count($credit_card_query_result) && count($uids)) {
          // $queue = \Drupal::queue('notify_credit_card_expiry_customer');
          // foreach($uids as $data) {
          //   // Create item to queue.
          //   $queue->createItem($data);
          // }
        }
    }
    
  }
  
  /**
   * @command AdvanceSubscriptionDrush:mailBeforeSubscriptionExpiryDrushCron
   * @aliases drush-mail-before-subscription-expiry
   */
  public function mailBeforeSubscriptionExpiryDrushCron() {
    /*
     * Need to send the mail to subscriber before their license expiry
     */
      $licenses = getLicensesToExpireBeforeWeek();
      if (count($licenses)) {
        $split_licenses = array_chunk($licenses, 100);
        $queue = \Drupal::queue('notify_subscriber_before_license_expiry');
        foreach($split_licenses as $license_data)  {
          $queue->createItem( $license_data);
        }
      }
    
  }
  

  /**
   * @command AdvanceSubscriptionDrush:SendRecurringOrderReceiptMail
   * @aliases drush-send-recurring-order-receipt-mail
   */
  public function SendRecurringOrderReceiptMail() {
    get_recurring_orders();
  }

}
