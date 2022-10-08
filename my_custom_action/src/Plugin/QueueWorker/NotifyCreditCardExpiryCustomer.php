<?php
/**
 * @file
 * Contains \Drupal\my_custom_action\Plugin\QueueWorker\NotifyCreditCardExpiryCustomer.
 */

namespace Drupal\my_custom_action\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Processes tasks for sending credit card expiry mail to customer.
 *
 * @QueueWorker(
 *   id = "notify_credit_card_expiry_customer",
 *   title = @Translation("Credit Card Expiration notification to customer"),
 *   cron = {"time" = 90}
 * )
 */
class NotifyCreditCardExpiryCustomer extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($item) {
    $current_month = date('m');
    $credit_card_next_month = $current_month + 1;
    $credit_card_year = date('Y');
    if($current_month == 12) {
      $credit_card_next_month = 1;
      $credit_card_year = date('Y') + 1;
    }
    $month_start = strtotime($credit_card_year . '-' . $credit_card_next_month . '-01');
    $last_day = date('t', $month_start);
    $credit_card_expires_timestamp = mktime(23, 59, 59, $credit_card_next_month, $last_day, $credit_card_year);
    $cc_expiry_info = date('M', strtotime('+1 month')) . ' '. $credit_card_year;
    $db = \Drupal::database();
    $site_url = \Drupal::request()->getSchemeAndHttpHost();
    foreach($item as $uid) {
      $query = $db->select('commerce_payment_method', 'cpm');
      $query->fields('cpm');
      // $query->join('commerce_payment', 'cp', 'cp.payment_method = cpm.method_id');
      // $query->join('commerce_payment_method__card_exp_month', 'ccm', 'cpm.method_id = ccm.entity_id and ccm.bundle = :bundle', [':bundle' => 'credit_card']);
      // $query->join('commerce_payment_method__card_exp_year', 'ccy', 'cpm.method_id = ccy.entity_id and ccy.bundle = :bundle', [':bundle' => 'credit_card']);
      // $query->condition('ccm.card_exp_month_value', $credit_card_next_month);
      // $query->condition('ccy.card_exp_year_value', $credit_card_year);
      $query->condition('cpm.uid', $uid);
      $query->condition('cpm.expires', $credit_card_expires_timestamp, '>');
      $credit_card_query_result = $query->execute()->fetchAll();
      if(count($credit_card_query_result) >= 1) {
        // There are more than 1 payment methods.
      } else {
        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'my_custom_action';
        $key = 'notify_credit_card_expiry_to_customer';
        $user_object = \Drupal\user\Entity\User::load($uid);
        if(is_object($user_object)) {
          if(isset($user_object->get('field_name_first')->getValue()[0]['value']) && $user_object->get('field_name_first')->getValue()[0]['value']) {
            $users_first_name = $user_object->get('field_name_first')->getValue()[0]['value'];
            if(isset($user_object->get('field_name_last')->getValue()[0]['value']) && $user_object->get('field_name_last')->getValue()[0]['value']) {
              $users_first_name .= ' '.$user_object->get('field_name_last')->getValue()[0]['value'];
            }
          } else {
            $users_first_name = $user_object->getUsername();
          }
          $to = $user_object->getEmail();
          $url = Url::fromRoute('entity.commerce_payment_method.collection', ['user' => $uid]);
          $payment_link = Link::fromTextAndUrl('Payment Methods', $url)->toString();
          // $params['message'] = "Dear $users_first_name, your credit card seems to be expired on month $cc_expiry_info. Please update your latest credit details in your $payment_link page.<br /><br /><b>Disclaimer :</b>If you have already updated your latest credit card, please ignore this mail.";
          $params['message'] = 'Dear '.$users_first_name .', your credit card seems to be expired on month '. $cc_expiry_info.'. Please login to the <a href="'.$site_url.'"> site </a>, navigate to your Dashboard page to add the latest credit card details.';
          $params['subject'] = "Credit Card expiration notification mail from Greenwood Commonwealth";
          $langcode = \Drupal::currentUser()->getPreferredLangcode();
          $send = true;
          $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
        }
      }
    }
  }
}