<?php

namespace Drupal\my_custom_action\EventSubscriber;

use Drupal\commerce_order\Mail\OrderReceiptMailInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Sends a receipt email when an order is placed.
 */
class PayBillOrderReceiptSubscriber implements EventSubscriberInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The order receipt mail.
   *
   * @var \Drupal\commerce_order\Mail\OrderReceiptMailInterface
   */
  protected $orderReceiptMail;

  /**
   * Constructs a new OrderReceiptSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_order\Mail\OrderReceiptMailInterface $order_receipt_mail
   *   The mail handler.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, OrderReceiptMailInterface $order_receipt_mail) {
    $this->entityTypeManager = $entity_type_manager;
    $this->orderReceiptMail = $order_receipt_mail;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = ['commerce_order.place.post_transition' => ['sendPayBillOrderReceipt', -100]];
    return $events;
  }

  /**
   * Sends an order receipt email.
   *
   * @param \Drupal\state_machine\Event\WorkflowTransitionEvent $event
   *   The event we subscribed to.
   * When user is paying bill for Print & Online subscription with out logined into the site, it will be considered as Anonymous order and mail is not going to the recipient mail address. So we need to send mail by custom way. 
   */
  public function sendPayBillOrderReceipt(WorkflowTransitionEvent $event) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $event->getEntity();
    if($order->bundle() == 'pay_bill') {
      if(!$loggined_user = \Drupal::CurrentUser()->id()) {
        if(count($order->getItems())) {
          $commerce_order_item = $order->getItems();
          if(count($commerce_order_item[0]->field_email_address->getValue()) && isset($commerce_order_item[0]->field_email_address->getValue()[0])) {
            $pay_bill_email_address = $commerce_order_item[0]->field_email_address->getValue()[0]['value'];
            $database = \Drupal::database();
            $query = $database->select('users_field_data', 'u');
            $query->condition('u.mail', $pay_bill_email_address);
            $query->fields('u', ['uid']);
            $uid = $query->execute()->fetchField();
            $update = \Drupal::database()->update('commerce_order');
            $update->fields(array('uid' => $uid, 'mail' => $pay_bill_email_address));
            $update->condition('order_id', $order->id());
            $update->execute();
            // $pay_bill_email_address = $result[0]->mail;
            // $pay_bill_user = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['mail'=> $pay_bill_email_address]);
            // // $order->setCustomer($pay_bill_user);
            // $user_object = reset($pay_bill_user);
            // $order->setCustomerId($user_object->id());
            // $order->setEmail($user_object->getEmail());
            // $order->save();
          } else {
            $user_name_value = $commerce_order_item[0]->field_user_name->getValue()[0]['value'];
            $database = \Drupal::database();
            $query = $database->select('users_field_data', 'u');
            $query->condition('u.name', $user_name_value);
            $query->fields('u', ['mail', 'uid']);
            $result = $query->execute()->fetchAll();
            $pay_bill_email_address = $result[0]->mail;
            $uid = $result[0]->uid;
            $update = \Drupal::database()->update('commerce_order');
            $update->fields(array('uid' => $uid, 'mail' => $pay_bill_email_address));
            $update->condition('order_id', $order->id());
            $update->execute();
          }
          if($pay_bill_email_address) {
            $order_type_storage = $this->entityTypeManager->getStorage('commerce_order_type');
            /** @var \Drupal\commerce_order\Entity\OrderTypeInterface $order_type */
            $order_type = $order_type_storage->load($order->bundle());
            if ($order_type->shouldSendReceipt()) {
              $this->orderReceiptMail->send($order, $pay_bill_email_address, $order_type->getReceiptBcc());
            }
          }
        }
      }
    } else {
      if($uid = \Drupal::CurrentUser()->id()) {
        if(is_object($order_billing_data = $order->getBillingProfile())) {
          $billing_address_data = $order_billing_data->address->getValue();
          if(count($billing_address_data)) {
            $loggined_user_data = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
            $first_name_data = $loggined_user_data->get('field_name_first')->getValue();
            if(!count($first_name_data)) {
              if($billing_address_data[0]['given_name']) {
                $loggined_user_data->set('field_name_first', ucwords($billing_address_data[0]['given_name']));
              }
            }
            $last_name_data = $loggined_user_data->get('field_name_last')->getValue();
            if(!count($last_name_data)) {
              if($billing_address_data[0]['family_name']) {
                $loggined_user_data->set('field_name_last', ucwords($billing_address_data[0]['family_name']));
              }
            }
            // Address field
            $user_current_address = $loggined_user_data->field_address->getValue();
            if(!count($user_current_address)) {
              $loggined_user_data->field_address = array(
                "country_code" => $billing_address_data[0]['country_code'],
                "address_line1" => $billing_address_data[0]['address_line1'],
                "locality" => $billing_address_data[0]['locality'],
                "administrative_area" => $billing_address_data[0]['administrative_area'],
                "postal_code" => $billing_address_data[0]['postal_code'],
              );
            }
            $loggined_user_data->save();
          }
        }
      }
    }
  }

}
