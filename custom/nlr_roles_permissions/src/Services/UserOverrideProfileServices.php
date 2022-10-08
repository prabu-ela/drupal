<?php

namespace Drupal\nlr_roles_permissions\Services;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class UserOverrideProfileServices is service to get user override.
 *
 * @pakage Drupal\nlr_roles_permissions\Services
 */
class UserOverrideProfileServices {

  /**
   * The user list.
   *
   * @var currentUserDrupal\Core\Session\AccountProxyInterface
   *  Srotes user id.
   */
  protected $currentUser;
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * CustomService constructor.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   A user interface.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager insterface.
   */
  public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entityTypeManager) {
    $this->currentUser = $currentUser;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Function to get current user overrid id.
   */
  public function getProfileOverride() {
    // Getting roles of the logged in user.
    $roles = $this->currentUser->getRoles();

    // Checking logged in user as administrator.
    if (in_array("moderator", $roles)) {

      // Getting profile data.
      $profile = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());
      $override = $profile->user_override_profiles->entity;

      // Getting profile field.
      if (!empty($override->field_override)) {
        $result = $override->get('field_override')->getValue();
        if (strpos($result['0']['value'], '+') !== FALSE) {
          $result = explode("+", $result['0']['value']);
          return $result;
        }
        if (in_array('all', $result['0'])) {
          $result['0']['value'] = NULL;
        }
        return $result['0']['value'];
      }
    }
    return $this->currentUser->id();
  }

}
