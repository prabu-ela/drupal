<?php

namespace Drupal\nofollow_noindex\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class WhitelistRoutesConfigForm.
 */
class RoutesConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'nofollow_noindex.routes',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'nofollow_noindex_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('nofollow_noindex.routes');
    $form['enable_nofollow'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable no-follow no-index'),
      '#description' => $this->t('Enable nofollow noindex on the below routes.'),
      '#default_value' => $config->get('enable_nofollow'),
    ];
    $form['disable_routes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Add nofollow noindex Routes'),
      '#default_value' => 'qa.*',
      '#description' => $this->t('A list of routes that you want to be add no-follow no-index ( enter each item per line). Wildcard "*" is supported. <br><b>Domain path:</b> A list of host routes (with out http:// or https://). If the domain name link https://qa.example.com means just add qa.*<br><u>Example:</u><br>local.*<br>qa.*<br><b>Relative path:</b> A list of Internal url with slash(/)<br><u>Example:</u><br>/admin/*<br>/news/*<br/>/register/webinar'),
      '#default_value' => $config->get('disable_routes'),
    ];

    $form['noindex_disable_routes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Add noindex Routes'),
      '#default_value' => 'qa.*',
      '#description' => $this->t('A list of routes that you want to be add no-index ( enter each item per line). Wildcard "*" is supported. <br><b>Domain path:</b> A list of host routes (with out http:// or https://). If the domain name link https://qa.example.com means just add qa.*<br><u>Example:</u><br>local.*<br>qa.*<br><b>Relative path:</b> A list of Internal url with slash(/)<br><u>Example:</u><br>/admin/*<br>/news/*<br/>/register/webinar'),
      '#default_value' => $config->get('noindex_disable_routes'),
    ];

    $form['nofollow_disable_routes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Add nofollow Routes'),
      '#default_value' => 'qa.*',
      '#description' => $this->t('A list of routes that you want to be add no-follow  ( enter each item per line). Wildcard "*" is supported. <br><b>Domain path:</b> A list of host routes (with out http:// or https://). If the domain name link https://qa.example.com means just add qa.*<br><u>Example:</u><br>local.*<br>qa.*<br><b>Relative path:</b> A list of Internal url with slash(/)<br><u>Example:</u><br>/admin/*<br>/news/*<br/>/register/webinar'),
      '#default_value' => $config->get('nofollow_disable_routes'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('nofollow_noindex.routes')
      ->set('enable_nofollow', $form_state->getValue('enable_nofollow'))
      ->set('disable_routes', $form_state->getValue('disable_routes'))
      ->set('noindex_disable_routes', $form_state->getValue('noindex_disable_routes'))
      ->set('nofollow_disable_routes', $form_state->getValue('nofollow_disable_routes'))
      ->save();
  }

}
