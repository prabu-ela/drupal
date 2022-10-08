<?php

namespace Drupal\nlr_moderator_entity_queue\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Moderator to add or remove entity queue.
 */
class ModeratorEntityQueueController extends ControllerBase {

  /**
   * EntityTypeManagerInterface service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entitySubqueue;

  /**
   * Constructs a EntityTypeManagerInterface object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entitySubqueue
   *   The Entityqueue service.
   */
  public function __construct(EntityTypeManagerInterface $entitySubqueue) {
    $this->entitySubqueue = $entitySubqueue;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Add/Remove Entiry queue data.
   *
   * Returns True/False.
   */
  public function manipulateQueueNode($qid = FALSE, $nid = FALSE) {
    // $flag 0 for instert node & $flag 1 for update.
    $flag = 0;

    if (!empty($qid) && !empty($qid)) {

      // Fetching data from EntityQueue.
      $entitySubqueue = $this->entitySubqueue->getStorage('entity_subqueue')->load($qid);
      $items = $entitySubqueue->get('items')->getValue();

      // Searching for Queue list.
      if (!empty($items)) {
        $result = [];
        $i = 0;
        foreach ($items as $value) {
          if ($value['target_id'] == $nid) {
            unset($items[$i]);
            $flag = 1;
          }
          $i++;
        }

        // Entity Queue insertion.
        if ($flag == 0) {
          $nodeInsert['target_id'] = $nid;
          array_push($items, $nodeInsert);
        }
      }

      // Saving the entity queue data.
      $entitySubqueue->set('items', $items);
      $entitySubqueue->save();
      return new JsonResponse([
        'response' => TRUE,
        'flag' => $flag,
        'nid' => $nid,
      ], 200);
    }
    return new JsonResponse([
      'response' => FALSE,
      'flag' => $flag,
      'nid' => $nid,
      'message' => 'Failed to add data',
    ], 201);
  }

}
