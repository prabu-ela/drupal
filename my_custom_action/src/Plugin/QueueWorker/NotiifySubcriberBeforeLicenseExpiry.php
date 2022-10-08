<?php
/**
 * @file
 * Contains \Drupal\my_custom_action\Plugin\QueueWorker\NotiifySubcriberBeforeLicenseExpiry.
 */

namespace Drupal\my_custom_action\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Processes tasks for sending mail to subscriber before their license expiry
 *
 * @QueueWorker(
 *   id = "notify_subscriber_before_license_expiry",
 *   title = @Translation("Sent license expiration notification mail to subcriber before it expires"),
 *   cron = {"time" = 90}
 * )
 */
class NotiifySubcriberBeforeLicenseExpiry extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($item) {
    $site_url = \Drupal::request()->getScheme() . '://' . \Drupal::request()->getHost();
    // $port_no = ':'.\Drupal::request()->getPort();
    foreach($item as $license) {
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'my_custom_action';
      $key = 'notify_subscriber_before_license_expiry';
      $commerce_license = \Drupal::entityTypeManager()->getStorage('commerce_license')->load($license);
      if(is_object($commerce_license->get('uid')->entity)) {
        // $request_time = \Drupal::service('datetime.time')->getRequestTime();
        $active_license_expires_time = $commerce_license->getExpiresTime();
        $uid = $commerce_license->getOwnerId();
        $query = \Drupal::service('entity_type.manager')->getStorage('commerce_license')
        ->getQuery()
        ->condition('state', 'active')
        ->condition('uid', $uid);
        // ->condition('expires', $request_time, '>=')
        $group = $query->orConditionGroup()
        ->condition('expires', $active_license_expires_time, '>')
        ->condition('expires', 0);
        $query->condition($group);
        $query->range(0, 1);
        $license_ids = $query->execute();
        if(!count($license_ids)) {
          $to = $commerce_license->get('uid')->entity->getEmail();
          $expire_date = date('M d, Y', $commerce_license->getExpiresTime());
          // $user_first_name = users_first_name($commerce_license->get('uid')->entity->id());
          $user_first_name = '';
          if(count($commerce_license->get('uid')->entity->get('field_name_first')->getValue())) {
            if($commerce_license->get('uid')->entity->get('field_name_first')->getValue()[0]['value']) {
              $user_first_name = ucfirst($commerce_license->get('uid')->entity->get('field_name_first')->getValue()[0]['value']);
            }
          }
          if(count($commerce_license->get('uid')->entity->get('field_name_last')->getValue())) {
            if($commerce_license->get('uid')->entity->get('field_name_last')->getValue()[0]['value']) {
              $user_first_name .= ' '.$commerce_license->get('uid')->entity->get('field_name_last')->getValue()[0]['value'];
            }
          }
          if(trim($user_first_name) == '') {
            $user_first_name = ucfirst($commerce_license->get('uid')->entity->getAccountName());
          }
          $license_subscription_type = $commerce_license->get('product_variation')->entity->bundle();
          $subscription_label = '';
          $subscription_mail_subject = '';
          if($license_subscription_type == 'fixed_products') {
            $subscription_label = 'Online Only';
            $subscription_mail_subject = ' Online Only ';
          } else if($license_subscription_type == 'print_subscription') {
            $subscription_label = 'Print & Online';
            $subscription_mail_subject = ' Print & Online ';
          }
          $params['cc'] = \Drupal::config('system.site')->get('mail');
          $params['message'] = t("Dear $user_first_name, your $subscription_label subscription to <a href='@subscription-site'>gwcommonwealth.com</a> is going to expire on $expire_date. You may extend it by <a href='@user-subscription-url'>clicking here</a> before the subscription expires.", array('@subscription-site' => $site_url, '@user-subscription-url' => $site_url.'/gwc-subscriptions'));
          $params['subject'] = "Greenwood Commonwealth $subscription_mail_subject Subscription Expiration";
          $langcode = \Drupal::currentUser()->getPreferredLangcode();
          $send = true;
          $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
        }
      }
    }
  }
}