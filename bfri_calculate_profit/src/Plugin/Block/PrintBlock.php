<?php

namespace Drupal\bfri_calculate_profit\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'calculate_profit' block.
 *
 * @Block(
 *   id = "print_block",
 *   admin_label = @Translation("Print Block"),
 *   category = @Translation("Custom Print Block")
 * )
 */
class PrintBlock extends BlockBase {

  /**
   * Building the Form.
   */
  public function build() {
    return [
      '#markup' => '<div class=btn btn-secondary id=print-this-area>Print</div>',
      '#attached' => [
        'library' => 'bfri_calculate_profit/custom_print',
      ],
    ];
  }

}
