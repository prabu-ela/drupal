<?php

namespace Drupal\my_custom_action\Form;

// use Drupal\Core\Entity\ContentEntityInterface;
// use Drupal\Core\Form\FormBase;
// use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
/**
 * Defines a confirmation form to confirm deletion of a commerce_recurring subscription by id.
 */
class LicenseCancelConfirmFormByUser extends ConfirmFormBase {
  //protected $license_id;
  public $uid;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return "user_license_cancel_form";
  }
  
  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    // return $this->t('Are you sure you want to cancel the %label subscription?', [
    //   '%label' => '',
    // ]);
    return $this->t('Are you sure you want to cancel the License?');
  }

  /**
   * {@inheritdoc}
   */
  // public function getCancelText() {
  //   return $this->t('Keep License Active');
  // }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('view.my_subscription.page_1', ['user' => $this->uid]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, string $license_id = NULL) {
    //$this->license_id = $id;
    $load_license_object = \Drupal::entityTypeManager()->getStorage('commerce_license');
    $license_object = $load_license_object->load($license_id);
    $license = $license_object;
    $state_item = $license->get('state')->first();
    assert($license instanceof StateTransitionFormInterface);

    // Check if the subscription is already canceled.
    // if ($subscription->getState()->getId() == 'canceled') {
    //   return [
    //     'description' => [
    //       '#markup' => $this->t('The subscription has already been canceled.'),
    //     ],
    //   ];
    // }
    $form['license_id'] = array(
      '#type' => 'value',
      '#value' => $license_id,
    );
    $form['uid'] = array(
      '#type' => 'value',
      '#value' => $license->getOwnerId()
    );
    $this->uid = $license->getOwnerId();
    foreach($state_item->getTransitions() as $transition_id => $transition) {
      if($transition_id == 'cancel') {
        $form['transitions'] = array(
          '#type' => 'value',
          '#value' => $transition
        );
      }
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $load_license_object = \Drupal::entityTypeManager()->getStorage('commerce_license');
    $license_object = $load_license_object->load($form_state->getValue('license_id'));
    $license = $license_object;
    assert($license instanceof StateTransitionFormInterface);
    //$state_item = $license->get('state')->first();
    //$triggering_element = $form_state->getTriggeringElement();
    /** @var \Drupal\state_machine\Plugin\Field\FieldType\StateItemInterface $state_item */
    $state_item = $license->get('state')->first();
    $state_item->applyTransition($form_state->getValue('transitions'));
    $license->save();
    $form_state->setRedirect('view.my_subscription.page_1', ['user' => $form_state->getValue('uid')]);
  }
}
