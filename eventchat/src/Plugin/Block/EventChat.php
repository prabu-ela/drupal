<?php

namespace Drupal\eventchat\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;

/**
 * Provides a 'eventchat' block.
 *
 * @Block(
 *   id = "eventchat_block",
 *   admin_label = @Translation("Event Chat Block"),
 *   category = @Translation("Custom Event Chat  block")
 * )
 */
class EventChat extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    global $base_path;
    $html_assign = [];
    $html_unassign = [];
    $exhibit_manager = [];
    $database = \Drupal::database();
    $query = $database->query("SELECT *  FROM eventchat_queue");
    $result = $query->fetchAll();

    foreach ($result as $rlt) {
      if ($rlt->status == 0) {
        $user = User::load($rlt->uid);
        $html_unassign[] = [
          'username' => $user->get('name')->value,
          'message' => $rlt->details,
          'id' => $rlt->qid,
        ];
      }
      elseif ($rlt->status == 1) {
        $user = User::load($rlt->uid);
        $html_assign[] = [
          'username' => $user->get('name')->value,
          'message' => $rlt->details,
          'id' => $rlt->qid,
        ];
      }
    }

    $ids = \Drupal::entityQuery('user')
      ->condition('status', 1)
      ->condition('roles', 'exhibit_booth_manager')
      ->execute();
    $users = User::loadMultiple($ids);
    foreach ($users as $user) {
      $exhibit_manager[] = [
        'exhibit_username' => $user->get('name')->value,
        'exhibit_user_id' => $user->get('uid')->value,
      ];
    }

    $roles = \Drupal::currentUser()->getRoles();
    $exhibit_user_check = 0;
    if (in_array('exhibit_booth_manager', $roles)) {
      $exhibit_user_check = 1;
    }

    $renderable = [
      '#theme' => 'agent_dashboard',
      '#assign' => $html_assign,
      '#unassign' => $html_unassign,
      '#exhibit_manager' => $exhibit_manager,
      '#user_tye' => $exhibit_user_check,
    ];
    return $renderable;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
