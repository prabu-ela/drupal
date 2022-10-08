<?php

namespace Drupal\bfri_calculate_profit\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'calculate_profit' block.
 *
 * @Block(
 *   id = "calculate_profilt_block",
 *   admin_label = @Translation("Calculate Your Profit"),
 *   category = @Translation("Custom Calculate Your Profit Block")
 * )
 */
class CalculateProfitBlock extends BlockBase {

  /**
   * Building the Form.
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\bfri_calculate_profit\Form\CalculateProfitForm');
    return $form;
  }

}
