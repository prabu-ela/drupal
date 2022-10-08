<?php

namespace Drupal\event_permission\Services;

use Drupal\node\NodeInterface;
use Drupal\node\Entity\Node;

/**
 * Page data is used for the get data.
 */
class Pagedata {

  /**
   * Resource Center Page.
   */
  public function resourcecenter($nid) {
    $database = \Drupal::database();
    $query = $database->query("SELECT * FROM node__field_register_with_events WHERE bundle = 'resource_center' AND field_register_with_events_target_id = $nid ");
    $result = $query->fetchAll();
    $resource_center = Node::load($result[0]->entity_id);
    return $resource_center;
  }

  /**
   * Exihibit Hall Page.
   */
  public function exihibit_hall($nid) {
    $database = \Drupal::database();
    $query = $database->query("SELECT * FROM node__field_register_with_events WHERE bundle = 'booth' AND field_register_with_events_target_id = $nid ");
    $result = $query->fetchAll();
    $hall = [];
    foreach ($result as $rnid) {
      $exihibit_hall = Node::load($rnid->entity_id);
      if ($exihibit_hall instanceof NodeInterface) {
        $hall[] = [
          'color' => $exihibit_hall->get('field_color_scheme')->getValue(),
          'title' => $exihibit_hall->get('title')->getValue(),
          'id'  => $exihibit_hall->get('nid')->getValue(),
          'banner' => ($exihibit_hall->get('field_top_banner_graphic')->target_id == NULL) ? '' : file_create_url($exihibit_hall->field_top_banner_graphic->entity->getFileUri()),

        ];
      }
    }
    $hallsp = [];
    foreach ($hall as $hallsplit) {
      $hallsp[] = [
        'color' => '/sites/default/files/images/booths/' . $hallsplit['color'][0]['value'] . '.png',
        'title' => $hallsplit['title'][0]['value'],
        'id'  => $hallsplit['id'][0]['value'],
        'banner' => $hallsplit['banner'],
      ];
    }

    return $hallsp;
  }

}
