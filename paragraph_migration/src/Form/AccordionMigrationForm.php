<?php

namespace Drupal\paragraph_migration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ParagraphMigrationForm.
 *
 * @package Drupal\paragraph_migration\Form
 */
class AccordionMigrationForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'accordion';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['migrate_accordion'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Migrate accordion'),
    );
 
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('paragraph_migration')->getPath();
    $file = fopen($module_path . '/data/brochure_content_migrate.csv', 'r');
    if (!$file) {
      $form_state->setErrorByName('migrate_accordion', $this->t('CSV file not found.'));
    }

  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('paragraph_migration')->getPath();
    $file = fopen($module_path . '/data/brochure_content_migrate.csv', 'r');
    if ($file) {
      $paragraphs_array = [];
      while (($line = fgetcsv($file)) !== FALSE) {
        //$line is an array of the csv elements
        $paragraphs_array[] = $line;
      }
      //echo "<pre>";
      $paragraphs_array = array($paragraphs_array);
      //print_r($paragraphs_array);
     // echo "</pre>";
      
      fclose($file);
     
      $batch = [
        'title' => t('Migrating Paragraphs...'),
        'operations' => [],
        //'finished' => '\Drupal\paragraph_migration\paragraph_migration\MigrateParagrph::deleteNodeExampleFinishedCallback',
      ];
      foreach ($paragraphs_array as $key => $value) {

        $batch['operations'][] = [
            '\Drupal\paragraph_migration\Controller\MigrateAccordion::migrateAccordion',
            array($value)
          ];
      }
//dump($paragraphs_array);die;
          

      batch_set($batch);
    }
  }
}