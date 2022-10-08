<?php

namespace Drupal\chat_mod\Controller;

use Drupal\Core\Controller\ControllerBase;

use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Chatmod for the lounge page open chat.
 */
class ChatmodController extends ControllerBase {

  /**
   * Event for the lobby page afte login.
   */
  public function callChaturlAfterlogin() {
    global $base_path;
    $user_id = \Drupal::currentUser()->id();
    $user = User::load(\Drupal::currentUser()->id());
    $roles = $user->getRoles();

    $nid = $user->get('field_register_with_events')->first()->getValue()['target_id'];
    if (!empty($nid)) {
      $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $nid);
      if (!empty($alias)) {
        $pathslashremove = str_replace('/', '', $alias);
        $path = $base_path . '' . $pathslashremove;
        $response = new RedirectResponse($path);
        $response->send();
      }
      else {
        $path = $base_path . 'user/' . $user_id . '/edit';
        $response = new RedirectResponse($path);
        $response->send();
        \Drupal::messenger()->addMessage('Please Select The Event', 'error');
      }
    }
    else {
      $path = $base_path . 'user/' . $user_id . '/edit';
      $response = new RedirectResponse($path);
      $response->send();
      \Drupal::messenger()->addMessage('Please Select The Event', 'error');
    }

  }

  /**
   * Reset password for the change password.
   */
  public function callChaturlAfterloginreset() {
    global $base_path;
    $user_id = \Drupal::currentUser()->id();
    $path = $base_path . 'user/' . $user_id . '/change-password';
    $response = new RedirectResponse($path);
    $response->send();
  }

}
