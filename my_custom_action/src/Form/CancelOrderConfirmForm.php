<?php

namespace Drupal\my_custom_action\Form;


use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Defines a confirmation form to confirm deletion of a commerce order by order id from url
 */
class CancelOrderConfirmForm extends ConfirmFormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'order_cancel_confirm_form';
  }
  
  public function getDescription() {
    return $this->t('');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to cancel this Order?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelText() {
    return $this->t('No');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.commerce_order.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $order_id = NULL) {
    $load_order_entity = \Drupal::entityTypeManager()->getStorage('commerce_order');
    $order_entity_object = $load_order_entity->load($order_id);
    // Check if the order is already canceled.
    if ($order_entity_object->getState()->getId() == 'canceled') {
      return [
        'description' => [
          '#markup' => $this->t('The Order has already been canceled.'),
        ],
      ];
    }
    $form['order_id'] = array(
      '#type' => 'value',
      '#value' => $order_id,
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $order_id = $form_state->getValue('order_id');
    /** @var \Drupal\state_machine\Plugin\Field\FieldType\StateItemInterface $state_item */
    $load_order_entity = \Drupal::entityTypeManager()->getStorage('commerce_order');
    $order_entity_object = $load_order_entity->load($order_id);
    $state_item = $order_entity_object->get('state')->first();
    $state_item->applyTransition($triggering_element['#transition']);
    $order_entity_object->save();
    $this->messenger()->addMessage($this->t('Order has been canceled successfully and please refund to the Order by clicking the Payments tab.'));
    $form_state->setRedirect('entity.commerce_order.canonical', ['commerce_order' => $order_id]);
  }
}
