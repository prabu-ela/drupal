<?php

namespace Drupal\bfri_calculate_profit\Form;

use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormBase;
use Drupal\node\NodeInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for Calcuate your Profit.
 */
class CalculateProfitForm extends FormBase {

  /**
   * Get Form Id.
   */
  public function getFormId() {
    return 'calculate_your_profit';
  }

  /**
   * Building Form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Calculating Profit.
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      // You can get nid and anything else you need from the node object.
      $nid = $node->id();
    }
    $type = '';
    $product_id = '';
    $avgPrice = '';
    $profitPercentage = '';
    $calcualte = 0;
    $productCalculation = [];
    if (!empty($nid)) {
      $node_data = Node::load($nid);
      $type = $node_data->bundle();

      if ($type == 'brochures' || $type == 'sales_incentive' || $type == 'big_events' || $type == 'mini_resource') {

        // Getting current path for profit calcuation.
        $current_path = \Drupal::service('path.current')->getPath();
        $result = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
        if ($result == '/frozen-food-fundraisers/dessert-days' || $result == '/frozen-food-fundraisers/mrs-fields-more' || 
          $result == '/cookie-dough-fundraisers/mrs-fields-cookie-dough') {
          $brochers_profit = $node_data->field_calculator_atr_onlin;

          // Getting Average Price.
          if (!empty($node_data->field_average_price)) {
            $avgPrice = $node_data->field_average_price->value;
          }
          
          $i = 0;
          foreach ($brochers_profit as $val) {
            $brochure_tag[$i]['min'] = $val->entity->field_min->value;
            $brochure_tag[$i]['max'] = $val->entity->field_max->value;
            $brochure_tag[$i]['profit'] = $val->entity->field_profit->value;
            $i++;
          }
          $form['#attached']['drupalSettings']['bfri_calculate_profit']['brochure_profit'] = $brochure_tag;
          $form['#attached']['drupalSettings']['bfri_calculate_profit']['avg_price'] = $avgPrice;
        }

        // Getting Profit Percentage.
        if (!empty($node_data->field_profit_percentage)) {
          $profitPercentage = $node_data->field_profit_percentage->value;
        }
        // Getting Average Price.
        if (!empty($node_data->field_average_price)) {
          $avgPrice = $node_data->field_average_price->value;
        }

        // Calculation percentage.
        if (!empty($profitPercentage)) {
          $calcualte = ($profitPercentage / 100) * $avgPrice;
        }
      }

      // Product content type calculations.
      if ($type == 'product_display') {
        if (!empty($node_data)) {
          if (!empty($node_data->field_product_reference->target_id)) {

            // Product Availability.
            $productAvailability = $node_data->field_available_or_not_abailable->value;
            if (!empty($productAvailability)) {
              // Show Quantity.
              $productShow = 0;
            }
            else {
              // Hide Quantity.
              $productShow = 1;
            }

            // Getting Packaging value.
            $product_id = $node_data->field_product_reference->target_id;

            $productCalculation['packaging'] = trim($node_data->field_product_reference->entity->field_packaging->value);
            $sellingPrice = trim($node_data->field_product_reference->entity->field_average_price->value);
            $profit = $node_data->field_product_reference->entity->field_calculator_attributes_para;
            $productCalculation['selling_prince'] = preg_replace('/[^0-9]/', '', $sellingPrice);

            $tag = [];
            $i = 0;
            foreach ($profit as $val) {
              $tag[$i]['min'] = $val->entity->field_min->value;
              $tag[$i]['max'] = $val->entity->field_max->value;
              $tag[$i]['profit'] = $val->entity->field_profit->value;
              $i++;
            }
          }
        }
      }
    }

    $case_needed = 'Cases Needed';
    $participant_placeholder = 'Items Sold/Participant';
    if ($product_id == 49 || $product_id == 46) {
      $case_needed = "Card Order";
      $participant_placeholder = 'Cards Sold/Participant';
    }

    $form['group_size'] = [
      '#type' => 'number',
      '#title' => $this->t('Group Size'),
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Number of participants',
        'min' => 0,
      ],

    ];

    $form['participant_goal'] = [
      '#type' => 'number',
      '#title' => $this->t('Participant Goal'),
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => $participant_placeholder,
        'min' => 0,
      ],
    ];

    // For Product Page.
    if ($type == 'product_display') {
      $form['casses_needed'] = [
        '#type' => 'textfield',
        '#title' => $this->t($case_needed),
        '#attributes' => ['readonly' => 'readonly'],
        '#default_value' => '',
      ];
    }

    $form['profit'] = [
      '#type' => 'textfield',
      '#title' => $this->t('PROFIT'),
      '#attributes' => ['readonly' => 'readonly'],
      '#default_value' => '$',
    ];

    // Attaching JS for Profit Calculation.
    $form['#attached']['library'][] = 'bfri_calculate_profit/bfri_calculate_profit.profit_calculate';
    $form['#attached']['drupalSettings']['bfri_calculate_profit']['calcualte'] = round($calcualte, 2);
    $form['#attached']['drupalSettings']['bfri_calculate_profit']['type'] = $type;
    $form['#attached']['drupalSettings']['bfri_calculate_profit']['product'] = $productCalculation;
    $form['#attached']['drupalSettings']['bfri_calculate_profit']['profit'] = $tag;
    $form['#attached']['drupalSettings']['bfri_calculate_profit']['min'] = array_column($tag, 'min');
    $form['#attached']['drupalSettings']['bfri_calculate_profit']['show_hide'] = $productShow;

    return $form;
  }

  /**
   * Empty submit for the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
