<?php

namespace Drupal\nlr_custom_print\Plugin\Block;

use Drupal\node\NodeInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'print' block.
 *
 * @Block(
 *   id = "print_email_block",
 *   admin_label = @Translation("Print Email Block"),
 *   category = @Translation("Custom Print Email block")
 * )
 */
class PrintEmail extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    global $base_path;
    $current_path = \Drupal::service('path.current')->getPath();
    $result = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
    $share = $base_path . $result;
    $user = \Drupal::currentUser()->id();

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $nid = $node->id();
    }

    $renderable = [
      '#theme' => 'print_email',
      '#share' => $share,
      '#nid' => $nid,
      '#base_url' => $base_path,
      '#uid' => $user,
    ];
    return $renderable;
  }

}
