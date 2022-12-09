<?php

namespace Drupal\mit_inventory_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides the inventory management filter form.
 */
class InventoryManagementForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inventory_management_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $header=[], $rows=[]) {


    // Getting all event content type.
    $nodesQry = \Drupal::database()->select('node_field_data', 'nfd');
    $nodesQry->fields('nfd', ['nid', 'title']);
    $nodesQry->condition('nfd.type', 'event');
    $nodes = $nodesQry->execute()->fetchAllKeyed(0, 1);
    
    $eventData = [];
    $eventData[''] = 'All events';
    foreach ($nodes as $key => $value) {
      $eventData[$key] = $value;
    }

    $form['event_title'] = [
      '#type' => 'select',
      '#title' => $this->t('Event'),
      '#options' => $eventData,
      '#default_value' => \Drupal::request()->get('event_title')
    ];


    // Getting all locations.
    $locationsQry = \Drupal::database()->select('commerce_stock_location_field_data', 'csl');
    $locationsQry->fields('csl', ['location_id', 'name']);
    $locations = $locationsQry->execute()->fetchAllKeyed(0, 1);
    
    $locData = [];
    $locData[''] = 'All locations';
    foreach ($locations as $key => $value) {
      $locData[$key] = $value;
    }

    $form['stock_location'] = [
      '#type' => 'select',
      '#title' => $this->t('Location'),
      '#options' => $locData,
      '#default_value' => \Drupal::request()->get('stock_location')
    ];

    // Getting all transaction type.
    $transData = [];
    $transData[''] = 'All transaction types';
    $transData['receiveStock'] = 'Receive stock';
    $transData['moveStock'] = 'Move stock';

    $form['transaction_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Transaction Type'),
      '#options' => $transData,
      '#default_value' => \Drupal::request()->get('transaction_type')
    ];

    // Getting all vendors.
    $vendorsQry = \Drupal::database()->select('inventory_transaction_type', 'itt');
    $vendorsQry->join('node_field_data', 'nfd', 'nfd.nid = itt.vendor_id');
    $vendorsQry->fields('itt', ['vendor_id']);
    $vendorsQry->fields('nfd', ['title']);
    $vendors = $vendorsQry->execute()->fetchAllKeyed(0, 1);
    
    $vendorData = [];
    $vendorData[''] = 'All vendors';
    foreach ($vendors as $key => $value) {
      $vendorData[$key] = $value;
    }

    $form['vendor_title'] = [
      '#type' => 'select',
      '#title' => $this->t('Vendor'),
      '#options' => $vendorData,
      '#default_value' => \Drupal::request()->get('vendor_title')
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];

    $form['#attached']['library'][] = 'mit_inventory_management/multi_select';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {


  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $url = Url::fromRoute('mit_inventory_management.inventory_management_form', [], ['query' => [
      'event_title' => $form_state->getValue('event_title'),
      'stock_location' => $form_state->getValue('stock_location'),
      'transaction_type' => $form_state->getValue('transaction_type'),
      'vendor_title' => $form_state->getValue('vendor_title')
      ]
    ]);
    $form_state->setRedirectUrl($url);
  }

}
