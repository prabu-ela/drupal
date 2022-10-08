<?php

namespace Drupal\paragraph_migration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ParagraphMigrationForm.
 *
 * @package Drupal\paragraph_migration\Form
 */
class TaxonomyMigrationForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'paragraph_migration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['migrate_taxonomy'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Migrate Taxonomy'),
    );

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('paragraph_migration')->getPath();
    $file = fopen($module_path . '/data/export_taxonomy.csv', 'r');
    if (!$file) {
      $form_state->setErrorByName('migrate_taxonomy', $this->t('CSV file not found.'));
    }
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('paragraph_migration')->getPath();
    $file = fopen($module_path . '/data/export_taxonomy.csv', 'r');
    if ($file) {
      $paragraphs_array = [];
      while (($line = fgetcsv($file)) !== FALSE) {
        //$line is an array of the csv elements
        $paragraphs_array[] = $line;
      }
      //echo "<pre>";
      $paragraphs_array = array_chunk($paragraphs_array, 3);
     // print_r($paragraphs_array);
     // echo "</pre>";
     // exit;
      
      fclose($file);
    
      $batch = [
        'title' => t('Migrating Taxonomy...'),
        'operations' => [],
        //'finished' => '\Drupal\paragraph_migration\paragraph_migration\MigrateParagrph::deleteNodeExampleFinishedCallback',
      ];
      foreach ($paragraphs_array as $key => $value) {
        $batch['operations'][] = [
            '\Drupal\paragraph_migration\Controller\MigrateTaxonomylinkmultiple::migrateResourcelink',
            array($value)
          ];
      }

          

      batch_set($batch);
    }
  }
}