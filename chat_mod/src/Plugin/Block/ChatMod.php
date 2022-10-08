<?php

namespace Drupal\chat_mod\Plugin\Block;

use Drupal\node\NodeInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'print' block.
 *
 * @Block(
 *   id = "chat_mod_block",
 *   admin_label = @Translation("Chat Mod Block"),
 *   category = @Translation("Custom Chat Mod  block")
 * )
 */
class ChatMod extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    global $base_path;
    $htmlcode = "";
    $node = \Drupal::routeMatch()->getParameter('node');
    if (!empty($node)) {
      if ($node->bundle() == "event") {
        $nid = "";
        if ($node instanceof NodeInterface) {
          // You can get nid and anything else you need from the node object.
          $nid = $node->id();
          $_SESSION['event_id'] = $nid;

          $database = \Drupal::database();
          $query = $database->query("SELECT nid,room_id,room_name  FROM chatmods_chat_rooms Where nid = $nid");
          $result = $query->fetchAll();
          $htmlcode = ($result[0]->room_id == "") ? 3 : $result[0]->room_id;
          $renderable = [
            '#theme' => 'chat_mod',
            '#share' => $htmlcode,
          ];
          return $renderable;

        }

      }
    }
    else {
      $nid = $_SESSION['event_id'];
      $database = \Drupal::database();
      $query = $database->query("SELECT nid,room_id,room_name  FROM chatmods_chat_rooms Where nid = $nid");
      $result = $query->fetchAll();
      $htmlcode = ($result[0]->room_id == "") ? 3 : $result[0]->room_id;
      $renderable = [
        '#theme' => 'chat_mod',
        '#share' => $htmlcode,
      ];
      return $renderable;
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
