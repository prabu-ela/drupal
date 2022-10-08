<?php

namespace Drupal\article_by_org\Plugin\Block;

use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "article_by_org_block",
 *   admin_label = @Translation("Article by org"),
 * )
 */
class ArticleByOrg extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    global $base_url;
    $label = '';
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $nid = $node->id();
    }
    $node = Node::load($nid);
    $org_info = $node->field_org_org_info->getValue();
    $orf_info_id = $org_info[0]["target_id"];
    $query = \Drupal::database()->select('node__field_org_org_info', 'a');
    $query->fields('a', ['entity_id']);
    $query->condition('field_org_org_info_target_id', $orf_info_id);
    $query->condition('entity_id', $nid, '!=');
    $result = $query->execute();
    $data = $result->fetchAll();
    $output = '';
    $output .= '<h3>Article by Orgranisation</h3>';
    foreach ($data as $auther_nid) {
      $author_node = Node::load($auther_nid->entity_id);
      $author_id = $author_node->uid->getValue();
      $uid = $author_id[0]["target_id"];
      $account = User::load($uid);
      $label1 = $author_node->label();
      $name = $account->get("name")->value;
      $output .= $label1;
      $output .= '<br>';
      $output .= '<p>' . $name . '</p>';
    }

    return [
     // '#theme' => 'custom_user_profile_block_template',
      '#type' => 'markup',
      '#markup' => $output,
     // '#output' => $output,
     // '#cache' => array(
     // 'max-age' => 0,
     // ),
    ];
  }

}
