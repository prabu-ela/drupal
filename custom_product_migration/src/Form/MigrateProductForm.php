<?php

namespace Drupal\custom_product_migration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MigrateProductForm.
 *
 * @package Drupal\custom_product_migration\Form
 */
class MigrateProductForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'product_migration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['migrate_product'] = [
      '#type' => 'submit',
      '#value' => $this->t('Migrate Product'),
    ];

    return $form;
  }

  /**
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('custom_product_migration')->getPath();
    $file = fopen($module_path . '/data/product_export.csv', 'r');
    if (!$file) {
      $form_state->setErrorByName('migrate_product', $this->t('CSV file not found.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('custom_product_migration')->getPath();
    $file = fopen($module_path . '/data/product_export.csv', 'r');
    if ($file) {
      $product_array = [];
      while (($line = fgetcsv($file)) !== FALSE) {
        // $line is an array of the csv elements
        $product_array[] = $line;
      }

      $key_product = $product_array[0];
      $product_array1 = [];
      foreach ($product_array as $kpa_val) {
        $product_array1[] = array_combine($key_product, $kpa_val);
      }

      $product_array = [$product_array1];
      echo "<pre>";
      print_r($product_array);
      echo "</pre>";
      // exit;.
      fclose($file);

      $batch = [
        'title' => t('Migrating Product...'),
        'operations' => [],
        // 'finished' => '\Drupal\paragraph_migration\paragraph_migration\MigrateParagrph::deleteNodeExampleFinishedCallback',
      ];
      foreach ($product_array as $key => $value) {
        $batch['operations'][] = [
          '\Drupal\custom_product_migration\Controller\MigrateProduct::migrateProduct',
            [$value],
        ];
      }

      batch_set($batch);
    }
  }

}
