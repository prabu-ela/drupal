<?php

namespace Drupal\eventchat\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EventchatSettingsForm.
 *
 * @package Drupal\eventchat\Form
 */
class EventchatSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'eventchat.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('eventchat.settings');

    $form['eventchat_general_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Settings'),
      '#open' => TRUE,
    ];
    $form['eventchat_general_settings']['eventchat_sid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site ID'),
      '#description' => $this->t('Can be any value. Cannot include spaces.'),
      '#default_value' => $config->get('eventchat_sid') ? $config->get('eventchat_sid') : NULL,
      '#required' => TRUE,
    ];
    $form['eventchat_general_settings']['eventchat_iflychat_app_id'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('iFlyChat APP ID'),
      '#default_value' => $config->get('eventchat_iflychat_app_id') ? $config->get('eventchat_iflychat_app_id') : NULL,
    ];
    $form['eventchat_general_settings']['eventchat_iflychat_api_key'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('iFlyChat API Key'),
      '#default_value' => $config->get('eventchat_iflychat_api_key') ? $config->get('eventchat_iflychat_api_key') : NULL,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('eventchat.settings')
      ->set('eventchat_sid', $form_state->getValue('eventchat_sid'))
      ->set('eventchat_iflychat_api_key', $form_state->getValue('eventchat_iflychat_api_key'))
      ->set('eventchat_iflychat_app_id', $form_state->getValue('eventchat_iflychat_app_id'))
      ->save();
  }

}
