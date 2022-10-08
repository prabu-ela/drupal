<?php

namespace Drupal\nlr_custom_print\Form;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Form for sending Email.
 */
class EmailForm extends FormBase {

  private $path;
  private $title;

  /**
   * @return string
   */
  public function getFormId() {
    return 'nlr_custom_print_email';
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL, $uid = NULL) {
    global $base_url;
    $user = '';
    if (!empty($uid) || $uid != 0) {
      $user = User::load($uid);
      $email = $user->get('mail')->value;
      $name = $user->get('name')->value;
    }

    // Loading Node data.
    $node = Node::load($nid);
    $this->path = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $nid, 'en');
    $this->title = $node->title->value;

    if (!empty($email)) {
      $form['email'] = [
        '#type' => 'email',
        '#title' => $this->t('Your Email'),
        '#attributes' => ['disabled' => 'disabled'],
        '#default_value' => $email,
      ];
    }
    else {
      $form['email'] = [
        '#type' => 'email',
        '#title' => $this->t('Your Email'),
        '#required' => TRUE,
      ];
    }

    if (!empty($name)) {
      $form['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Your Name'),
        '#attributes' => ['disabled' => 'disabled'],
        '#default_value' => $name,
      ];
    }
    else {
      $form['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Your Name'),
        '#required' => TRUE,
      ];
    }

    $form['to'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Send To'),
      '#description' => $this->t('Enter multiple addresses separated by commas(,).'),
      '#required' => TRUE,
    ];

    if (!empty($name)) {
      $form['subject'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Subject'),
        '#default_value' => $this->t($name . ' has sent you a message from The National Law Review'),
        '#required' => TRUE,
      ];
    }
    else {
      $form['subject'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Subject'),
        '#default_value' => $this->t('Someone has sent you a message from The National Law Review'),
        '#required' => TRUE,
      ];
    }

    $form['page'] = [
      '#type' => 'item',
      '#title' => $this->t('Page to be sent'),
      '#markup' => Link::fromTextAndUrl(($node->title->value), Url::fromUri($base_url . $this->path))->toString(),
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Your Message'),
      '#required' => TRUE,
    ];

    $form['teaser'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Send only the teaser'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send Email'),
    ];
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#attributes' => [
        'onclick' => 'window.location.assign("'.$base_url . $this->path.'");return false;',
      ],
    ];

    $form['back']['#attributes']['class'][0] = 'button btn btn-primary';
    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    global $base_url;
    $message['email'] = $form_state->getValue('email');
    $message['to'] = $form_state->getValue('to');
    $message['subject'] = $form_state->getValue('subject');
    $message['name'] = $form_state->getValue('name');
    $message['page'] = $form_state->getValue('page');
    $message['message'] = $form_state->getValue('message');
    $message['title'] = $this->title;
    $message['path'] = $base_url . $this->path;

    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'nlr_custom_print';
    $key = 'node_email';
    $langcode = 'en';

    $to = explode(',', $message['to']);

    foreach ($to as $value) {
      $sent = $mailManager->mail($module, $key, trim($value), $langcode, $message, NULL, TRUE);
    }
    if ($sent['result'] == TRUE) {
      $response = new RedirectResponse($message['path']);
      $response->send();
      \Drupal::messenger()->addMessage('Email Sent Successfully', 'status');
    }
    else {
      \Drupal::messenger()->addError(t('Unable to Sent Mail'));
    }
    return TRUE;
  }

}
