<?php

namespace Drupal\custom_product_migration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ParagraphMigrationForm.
 *
 * @package Drupal\custom_product_migration\Form
 */
class ParagraphMigrationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_product_migration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['migrate_paragraph'] = [
      '#type' => 'submit',
      '#value' => $this->t('Migrate Paragraph'),
    ];

    return $form;
  }

  /**
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('custom_product_migration')->getPath();
    $file = fopen($module_path . '/data/product_paragaraph.csv', 'r');
    if (!$file) {
      $form_state->setErrorByName('migrate_paragraph', $this->t('CSV file not found.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('custom_product_migration')->getPath();
    $file = fopen($module_path . '/data/product_paragaraph.csv', 'r');
    if ($file) {
      $paragraphs_array = [];
      while (($line = fgetcsv($file)) !== FALSE) {
        // $line is an array of the csv elements
        $paragraphs_array[] = $line;
      }
      // Echo "<pre>";.
      $paragraphs_array = array_chunk($paragraphs_array, 3);
      // print_r($paragraphs_array);
      //  echo "</pre>";.
      // exit;.
      fclose($file);

      $batch = [
        'title' => t('Migrating Paragraphs...'),
        'operations' => [],
        // 'finished' => '\Drupal\paragraph_migration\paragraph_migration\MigrateParagrph::deleteNodeExampleFinishedCallback',
      ];
      foreach ($paragraphs_array as $key => $value) {
        $batch['operations'][] = [
          '\Drupal\custom_product_migration\Controller\MigrateParagrph::migrateParagraph',
            [$value],
        ];
      }

      batch_set($batch);
    }
  }

}
