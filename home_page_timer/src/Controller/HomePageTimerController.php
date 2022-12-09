<?php

namespace Drupal\home_page_timer\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class for Home Page timer.
 */
class HomePageTimerController extends ControllerBase {

  /**
   * Getting the home page timer.
   */
  public static function getTimer() {
    $current_user = \Drupal::currentUser();

    if (!$current_user->isAnonymous()) {
      $uid = $current_user->id();
      $account = User::load($uid);
      if (!$account->get('field_cart_timer')->isEmpty()) {
        $user_date = $account->get('field_cart_timer')->value;
        $current_time = date("d-m-Y h:i:s");
        $from_time = strtotime($user_date);
        $to_time = strtotime($current_time);
        $diff_minutes = round(abs($from_time - $to_time) / 60, 2);
        echo $diff_minutes;
        die;
      }
    }
  }

  /**
   * Setting the Timer.
   */
  public static function setTimer() {
    // Timer Functionality for add to cart.
    $current_user = \Drupal::currentUser();
    if ($current_user->isAuthenticated()) {
      $uid = $current_user->id();
      $date = date("d-m-Y h:i:s");
      $account = User::load($uid);
      $account->field_cart_timer = $date;
      $account->save();
      return [
        "current_date" => $date,
      ];
    }
  }

}
