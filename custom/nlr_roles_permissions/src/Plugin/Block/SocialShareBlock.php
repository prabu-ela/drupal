<?php

namespace Drupal\nlr_roles_permissions\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'article' block.
 *
 * @Block(
 *   id = "socialshare_block",
 *   admin_label = @Translation("Social Share block"),
 *   category = @Translation("Custom social share block")
 * )
 */
class SocialShareBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    global $base_path;
    $current_path = \Drupal::service('path.current')->getPath();
    $result = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
    $share = $base_path . $result;
    $renderable = [
      '#theme' => 'social_share',
      '#share' => $share,
    ];
    return $renderable;
  }

}
