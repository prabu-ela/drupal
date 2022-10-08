<?php

namespace Drupal\my_custom_action\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\file\Entity\File;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_price\Price;
use \Drupal\commerce_store\Entity\Store;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_order\Entity\Order;

class MigrateSubscriptioncustomer extends FormBase {


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
      '#upload_location' => 'public://subscription-customer/',
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
 /* echo "<pre>";
    print_r($data);
  exit; */
    /*
     * Need to check whether the id from csv file has been imported and license has been created. To avoid duplicate licenses we need to check this. Storing the values in state variable and once the subscription is migrated, need to delete this state variable
     */
    $csv_source_ids_array = [];
    
    if(count($data)) {
      foreach($data as $row) {
        $database = \Drupal::database();    
       /**
        * Create Order item
        */
        $profileStorage = \Drupal::entityTypeManager()->getStorage('profile');
        $name = explode(" ", $row["Address - Full name"]);
        $profile_data = [
         'type' => 'customer',
         'profile_id' => $row['Profile ID'],
         'uid' => \Drupal::currentUser()->id(),
         'status'=> 1,
         'address' => [
           "langcode" => "",
           "country_code" => $row['Address - Country'],
           "administrative_area" => $row["Address - Administrative area (i.e. State / Province)"],
           "locality" => $row["Address - Locality (i.e. City)"],
           "dependent_locality" => null,
           "postal_code" => $row["Address - Postal code"],
           "sorting_code" => null,
           "address_line1" => $row["Address - Thoroughfare (i.e. Street address)"],
           "address_line2" => $row["Address - Premise (i.e. Apartment / Suite number)"],
           "organization" => $row["Address - Company"],
           "given_name" => $name[0],
           "additional_name" => null,
           "family_name" => $name[1],
          ],
        ];
        
        $new_profile = $profileStorage->create($profile_data);
        $new_profile->save();

        
      }
    }
    echo 'success';
    exit;
    if(count($operations)) {
      if(count($csv_source_ids_array)) {
        $csv_source_ids_array = $existing_migration_subscription_csv_id_array + $csv_source_ids_array;
        \Drupal::state()->set('my_custom_action.migration_subscription_csv_id', serialize($csv_source_ids_array));
      }
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\my_custom_action\CsvMigrateSubscriptionOrders::MigrateSubscriptionOrderCallback',
      );
      batch_set($batch);
    }
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
