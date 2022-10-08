<?php

namespace Drupal\bfri_calculate_profit\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\commerce_product\Entity\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Moderator to add or remove entity queue.
 */
class PurchaseOrderController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new PoductOrder.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
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
   * Calculating the Price by passing node id.
   *
   * @param mixed $nid
   *   The Node id.
   *
   * @return json
   *   Json data of the node id passed.
   *   \Drupal\Component\Serialization\JsonResponse $data.
   */
  public function calculatePrice($nid = '') {
    if (!empty($nid)) {
      $node_storage = $this->entityTypeManager->getStorage('node');
      $node = $node_storage->load($nid);

      // Getting product price from the node.
      $price_data = $node->field_product_reference->entity->field_average_price->value;
      $price = preg_replace('/[^0-9.]/', '', $price_data);

      // Calculating Min Quanity.
      $quanity = $node->field_product_reference->entity->field_calculator_attributes_para;
      $tag = [];
      foreach ($quanity as $val) {
        $tag[] = $val->entity->field_min->value;
      }
      $data['min'] = (int) min($tag);
      $data['max'] = (int) max($tag);

      // Product id.
      $product_id = $node->field_product_reference->target_id;

      // Product load.
      $product_load = Product::load((int) $product_id);

      // Variation id.
      $productVariationId = $product_load->getVariations();
      $productVariationLoad = $productVariationId[0]->field_price_table;

      $i = 0;
      foreach ($productVariationLoad as $val) {
        $data[$i]['amount'] = (int) $val->amount;
        $data[$i]['min_qty'] = (int) $val->min_qty;
        $data[$i]['max_qty'] = (int) $val->max_qty;
        $i++;
      }
      $data['status'] = TRUE;

      return new JsonResponse(
        [
          'response' => $data,
        ]
      );
    }

  }

}
