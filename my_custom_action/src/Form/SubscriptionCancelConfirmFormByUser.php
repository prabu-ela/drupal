<?php

namespace Drupal\my_custom_action\Form;

use Drupal\commerce_recurring\Entity\SubscriptionInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Defines a confirmation form to confirm deletion of a commerce_recurring subscription by id.
 */
class SubscriptionCancelConfirmFormByUser extends ConfirmFormBase {
  
  // protected $subscription_id;
  public $uid;
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return "user_subscription_cancel_form";
  }
  
  public function getDescription() {
    return $this->t('');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to cancel the subscription?');
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
    return new Url('view.my_subscription.page_1', ['user' => $this->uid]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, string $subscription_id = NULL) {
    // $this->subscription_id = $id;
    $load_subscription_object = \Drupal::entityTypeManager()->getStorage('commerce_subscription');
    $subscription_object = $load_subscription_object->load($subscription_id);
    $product_variation = $subscription_object->getPurchasedEntity();
    $subscription = $subscription_object;
    assert($subscription instanceof SubscriptionInterface);
    

    // Check if the subscription is already canceled.
    if ($subscription->getState()->getId() == 'canceled') {
      return [
        'description' => [
          '#markup' => $this->t('The subscription has already been canceled.'),
        ],
      ];
    }

    $form['subscription_id'] = array(
      '#type' => 'value',
      '#value' => $subscription_id,
    );
    $form['uid'] = array(
      '#type' => 'value',
      '#value' => $subscription->getOwnerId()
    );
    $this->uid = $subscription->getOwnerId();
    if($product_variation->bundle() == 'free_trial_to_rolling' && $subscription->getState()->getId() == 'trial') {
      $form['cancel_confirm_text'] = ['#type' => 'item', '#weight' => -1, '#title' => t(''), '#markup' => '<div class = "cancel-confirm-text">Are you sure you want to cancel the subscription?</div>'];
      $form['scheduled'] = [
        '#type' => 'radios',
        '#title' => $this->t('Cancellation date'),
        '#options' => [
          'now' => $this->t('Immediately'),
        ],
        '#default_value' => 'now',
        '#prefix' => '<div class="user-cancel-subscription">',
        '#suffix' => '</div>',
      ];
    } else {
      $end_date_timestamp = $subscription->getCurrentBillingPeriod()->getEndDate()->getTimestamp();
      $end_date = \Drupal::service('date.formatter')->format($end_date_timestamp, 'custom');
      $valid_end_date = date('M d Y', $end_date_timestamp);
      $form['cancel_confirm_text'] = ['#type' => 'item', '#weight' => -1, '#title' => t(''), '#markup' => '<div class = "cancel-confirm-text">Are you sure you want to cancel the subscription?</div>'];
      $form['scheduled'] = [
        '#type' => 'radios',
        '#title' => $this->t('Cancellation date'),
        '#options' => [
          'scheduled' => $this->t('End of the current billing period (@end_date)', [
            '@end_date' => $valid_end_date,
          ]),
        ],
        '#default_value' => 'scheduled',
        '#prefix' => '<div class="user-cancel-subscription">',
        '#suffix' => '<div class = "">Subscription will be scheduled to cancel on '. $valid_end_date .'</div></div>',
      ];
    }

    // Disable the 'scheduled' option if one has already been scheduled.
    // if ($subscription->hasScheduledChange('state', 'canceled')) {
    //   $form['scheduled']['scheduled'] = ['#disabled' => TRUE];
    //   $form['scheduled']['#default_value'] = 'now';
    //   $form['scheduled']['#description'] = $this->t('A cancellation has already been scheduled for @end_date.', [
    //     '@end_date' => $end_date,
    //   ]);

    
    // }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $load_subscription_object = \Drupal::entityTypeManager()->getStorage('commerce_subscription');
    $subscription_object = $load_subscription_object->load($form_state->getValue('subscription_id'));
    $subscription = $subscription_object;
    assert($subscription instanceof SubscriptionInterface);
    $scheduled = $form_state->getValue('scheduled') === 'scheduled';
    $subscription->cancel($scheduled);
    if ($scheduled) {
      $end_date = $subscription->getCurrentBillingPeriod()->getEndDate()->getTimestamp();
      $subscription->setEndTime($end_date);
    } else {
      $current_timestamp = \Drupal::time()->getCurrentTime();
      $subscription->setTrialEndTime($current_timestamp);
    }
    $subscription->save();
    if ($scheduled) {
      // $end_date_value = \Drupal::service('date.formatter')->format($end_date, 'custom');
      $end_date_value = date('M d Y', $end_date);
      $this->messenger()->addMessage($this->t('The subscription has been scheduled for cancellation on '. $end_date_value .'.'));
    } else {
      $this->messenger()->addMessage($this->t('The subscription has been canceled.'));
    }
    $form_state->setRedirect('view.my_subscription.page_1', ['user' => $form_state->getValue('uid')]);
  }

}
