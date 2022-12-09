<?php

namespace Drupal\mit_admin_page\Controller;

use Drupal\core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for entity operational link.
 */
class OperationLinkController extends ControllerBase {

  /**
   * Publishing the content.
   */
  public function publishNode($nid = []) {
    if (!empty($nid)) {

      // Getting return path.
      $returnPath = \Drupal::request()->query->get('destination');

      // Unpublishing the node.
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
      $node->set('status', 1);
      $node->save();

      // Redirecting to the destination page.
      $url = Url::fromUri('internal:' . $returnPath);
      $response = new RedirectResponse($url->toString());
      return $response->send();
    }
  }

  /**
   * Unpublishing the content.
   */
  public function unPublishNode($nid = []) {
    if (!empty($nid)) {

      // Getting return path.
      $returnPath = \Drupal::request()->query->get('destination');

      // Unpublishing the node.
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
      $node->set('status', 0);
      $node->save();

      // Redirecting to the destination page.
      $url = Url::fromUri('internal:' . $returnPath);
      $response = new RedirectResponse($url->toString());
      return $response->send();
    }
  }

  /**
   * Feature Tag.
   */
  public function feature($nid = [], $term = []) {

    if (!empty($nid)) {

      // Getting return path.
      $returnPath = \Drupal::request()->query->get('destination');

      // Loading all taxonomy term from the Tag category.
      $tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('tags');
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

      // Setting taxonomy termid
      foreach ($tree as $value) {
        if (strtolower(trim($value->name)) == strtolower(trim($term))) {
          $data['target_id'] = $value->tid;
          $node->set('field_availability', $data);
        }
      }
      $node->save();

      // Redirecting to the destination page.
      $url = Url::fromUri('internal:' . '/admin/content');
      $response = new RedirectResponse($url->toString());
      return $response->send();
    }
  }

}
