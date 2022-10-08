<?php

namespace Drupal\my_custom_action\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\file\Entity\File;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_shipping\Entity\Shipment;
use Drupal\commerce_shipping\Entity\ShippingMethod;
use Drupal\commerce_shipping\ShipmentItem;
use Drupal\physical\Weight;
use Drupal\physical\LengthUnit;
use Drupal\physical\WeightUnit;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Adjustment;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_price\Price;

class MigrateSubscriptionorders extends FormBase {


  public function getFormId() {
    return 'subscription_orders_import_form';
  }


  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];
    $form['#attributes'] = array('enctype' => 'multipart/form-data');
    $validators = array(
      'file_validate_extensions' => array('csv'),
    );
    $form['csv_file'] = array(
      '#type' => 'managed_file',
      '#name' => 'csv_file',
      '#title' => t('Upload CSV File'),
      '#size' => 20,
      '#description' => t('Upload CSV file to migrate Subscription Orders'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://subscription-orders/',
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /*
   * Form validation which validates below csv values
   * Product title should not be empty
   * Emails in presenter column should be a user, presenter of that domain
   * Price value should not be empty & it has to be in this format 10.00
  */

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $csv_file = $form_state->getValue('csv_file');
    if(!count($csv_file)) {
      $form_state->setErrorByName('csv_file', t('Please upload CSV file to migrate Subscription Orders'));
    } else {
      $file = File::load( $csv_file[0] );
      $csv_product_rows = $this->csvtoarray($file->getFileUri(), ',');
      if(!count($csv_product_rows)) {
        $form_state->setErrorByName('csv_file', t('CSV file is empty.'));
      }
    }
  }

  /*
   * Submit function which executes batch operations to create session products
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $csv_file = $form_state->getValue('csv_file');
    $file = File::load( $csv_file[0] );
    //$file->setPermanent();
    //$file->save();
    $operations = [];
    $data = $this->csvtoarray($file->getFileUri(), ',');
    /*
     * Need to check whether the id from csv file has been imported and license has been created. To avoid duplicate licenses we need to check this. Storing the values in state variable and once the subscription is migrated, need to delete this state variable
     */
    $csv_source_ids_array = [];
    $entity_manager = \Drupal::entityManager();
    $database = \Drupal::database();
    $order_number = array();
    $line_item = array();
    $orderid = "";
    $i = 0;
      if(count($data)) {
        $j = 0;
      foreach($data as $row) {
        $k=0;
        $database = \Drupal::database();
        $query = $database->select('commerce_product_variation_field_data', 'pv');
        $query->condition('pv.sku ', $row['SKU']);
        $query->fields('pv', ['variation_id']);
        $pv_result = $query->execute()->fetchAll();
        if(!empty($pv_result) || empty($row['SKU'])){
          $querynew = $database->select('commerce_order', 'co');
          $querynew->condition('co.order_number ', $row['Order number']);
          $querynew->fields('co', ['order_id']);
          $pv_result_new = $querynew->execute()->fetchAll();
          if(!empty($pv_result_new)){
            if(empty($row['SKU'])){
              $order = Order::load($pv_result_new[0]->order_id);
              $currency_code = $order->getStore()->getDefaultCurrencyCode();
              $tax_price = trim($row['Unitprice'], '$');
               $order->addAdjustment(new Adjustment([
                'type' => 'tax',
                'label' => $this->t('Sales tax'),
                'amount' => new Price((float) $tax_price, $currency_code),
                'source_id' => $pv_result_new[0]->order_id,
              ])); 
              $order->save();
            }else{
              $product_variation_id =  $pv_result[0]->variation_id; 
              $product_variation = $entity_manager->getStorage('commerce_product_variation')->load($product_variation_id);   
              /**
              * Create Order item
              */
              if($product_variation->label() === NULL){
                $product_title = '';
              }else {
                $product_title = $product_variation->label();
              }
              $order = Order::load($pv_result_new[0]->order_id);
              $currency_code = $order->getStore()->getDefaultCurrencyCode();
              $unit_price = trim($row['Unitprice'], '$');
              $order->addItem(\Drupal\commerce_order\Entity\OrderItem::create([
                'type' => 'default',
                'purchased_entity' => $product_variation,
                'quantity' => $row['Quantity'],
                'unit_price' => new Price((float) $unit_price, $currency_code),
                'title' => $product_title,
              ]));
              $order->save();
              $order->save();
            }
          } else {
            $product_variation_id =  $pv_result[0]->variation_id; 
            $product_variation = $entity_manager->getStorage('commerce_product_variation')->load($product_variation_id);   
            /**
            * Create Order item
            */
            if($product_variation->label() === NULL){
              $product_title = '';
            }else {
              $product_title = $product_variation->label();
            }
            $unit_price = trim($row['Unitprice'], '$');
            $order_item = \Drupal\commerce_order\Entity\OrderItem::create([
              'type' => 'default',
              'purchased_entity' => $product_variation,
              'quantity' => $row['Quantity'],
              'unit_price' => new Price((float) $unit_price, 'USD'),
              'title' => $product_title,
              // Omit these lines to preserve original product price.
              //'unit_price' => new Price(80, 'EUR'),
              // 'overridden_unit_price' => TRUE,
            ]);
            $order_item->save();
      
            $order = \Drupal\commerce_order\Entity\Order::create([
            'type' => 'default',
            'mail' => $row['E-mail'],
            'uid' => $row['Uid'],
            'ip_address' => '',
            'order_number' => $row['Order number'],
            'store_id' => 1,
            'order_items' => [$order_item],
            'placed' => $row['Created date'],
            'payment_gateway' => 'Braintree Hosted Fields',
            'checkout_step' => 'payment',
            'state' => $row['Order status'],
            ]);
            $order->save();
            
            // And finally we create shipping and billing profile.
            if(!empty($row['Profile_ID'])){
              $first_shipment = Shipment::create([
                'type' => 'default',
                'order_id' => $order->id(),
                'title' => 'Shipment',
                'state' => 'ready'
                ]);
              $first_shipment->save();
              $quantity = $order_item->getQuantity();
              $purchased_entity = $order_item->getPurchasedEntity();
              if ($purchased_entity->get('weight')->isEmpty()) {
                $weight = new Weight(1, WeightUnit::GRAM);
              }
              else {
                $weight_item = $purchased_entity->get('weight')->first();
                $weight = $weight_item->toMeasurement();
              }
              $shipment_item = new ShipmentItem([
                'order_item_id' => $order_item->id(),
                'title' => $purchased_entity->label(),
                'quantity' => $quantity,
                'weight' => $weight->multiply($quantity),
                'declared_value' => $order_item->getTotalPrice(),
              ]);
              $first_shipment->addItem($shipment_item);
    
              $profile = \Drupal\profile\Entity\Profile::create([
                'uid' => $row['Uid'],
                'type' => 'customer',
              ]);
              $profile->address->given_name = $row['Shipping_Firstname'];
              $profile->address->family_name = $row['Shipping_Lastname'];
              $profile->address->organization = $row['shipping_company'];
              $profile->address->country_code = $row['Shipping_Country'];
              $profile->address->administrative_area = $row['shipping_state'];
              $profile->address->locality = $row['shipping_city'];
              $profile->address->postal_code = $row['shipping_postal_code'];
              $profile->address->address_line1 = $row['shipping_streetaddress_1'];
              $profile->address->address_line2 = $row['shipping_streetaddress_2'];
              $profile->save();
              
              $billing_profile = \Drupal\profile\Entity\Profile::create([
                'type' => 'customer',
                'uid' => $row['Uid'],
              ]);
              $billing_profile->save();
              $billing_profile->address->given_name = $row['Billing_Firstname'];
              $billing_profile->address->family_name = $row['Billing_Lastname'];
              $billing_profile->address->organization = $row['Billing_Company'];
              $billing_profile->address->country_code = $row['Billing_Country'];
              $billing_profile->address->administrative_area = $row['Billing_State'];
              $billing_profile->address->locality = $row['Billing_City'];
              $billing_profile->address->postal_code = $row['biiling_postal'];
              $billing_profile->address->address_line1 = $row['billing_address1'];
              $billing_profile->address->address_line2 = $row['billing_address_2'];
              $billing_profile->field_phone_customer = $row['billing_phone_no'];
              $billing_profile->save();
              $order->setBillingProfile($billing_profile);
              $order->save();
    
              $first_shipment->setShippingProfile($profile);
              $first_shipment->save();
              $order->set('shipments', $first_shipment);
              $order->save();
              }
            }
          } 
       }
       /*$order_type_storage = \Drupal::entityTypeManager()->getStorage('commerce_order_type');
       $order_type = $order_type_storage->load($order->bundle());
       $number_pattern = $order_type->getNumberPattern();
       if ($number_pattern) {
         $order_number = $number_pattern->getPlugin()->generate($order);
       } else {
         $order_number = $order->id();
       }
       $order->setOrderNumber($order_number);
       $order->save(); */
      } 
      echo 'success';
      exit; 
  }

  public function saveOrder(array $order_array) {
    //echo "<pre>";
    //print_r($order_array);
  }

  public function savemultipleOrder(array $order_multiple_array) {
    echo "<pre>";
    print_r($order_multiple_array);
    
  }

 
 
  /*
   * Funtion which combine one csv row values and return as array
  */
  public function csvtoarray($filename='', $delimiter, $csv_first_row = '') {
    if(!file_exists($filename) || !is_readable($filename)) 
      return FALSE;
    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE ) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        if(!$header) {
          $header = $row;
        } else {
          $data[] = array_combine($header, $row);
        }
      }
      fclose($handle);
    }
    if(isset($csv_first_row) && $csv_first_row) {
      return $header;
    }
    return $data;
  }
}
