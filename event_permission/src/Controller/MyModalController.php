<?php

namespace Drupal\event_permission\Controller;

use Drupal\node\NodeInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;

/**
 * Used for the Mobile based video show.
 */
class MyModalController extends ControllerBase {

  /**
   * Open Modal for the mobile.
   */
  public function openMyModal() {
    $link = \Drupal::request()->query->get('link');

    $html = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "<iframe width=\"420\" style=\"display:block\" height=\"315\" class=\"mob_play\" id=\"mob_play\" src=\"https://www.youtube.com/embed/$1?autoplay=1&rel=0\" allow=\"autoplay\"  frameborder=\"0\" allowfullscreen></iframe>", $link);
    $response = new AjaxResponse();
    $modal_form = $html;
    $options = [
      'width' => '75%',
    ];
    $response->addCommand(new OpenModalDialogCommand('', $modal_form, $options));
    return $response;
  }

  /**
   * Get Node id.
   */
  public function getNodeid() {

    $node = \Drupal::routeMatch()->getParameter('node');
    $nid = "";
    if ($node instanceof NodeInterface) {
      // You can get nid and anything else you need from the node object.
      $nid = $node->id();
      if ($node->bundle() == "event") {
        $value = $node->get('field_youtube_url')->getValue();
        return $value;
      }
    }

  }

}
