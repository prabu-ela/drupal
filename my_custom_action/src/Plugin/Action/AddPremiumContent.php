<?php

namespace Drupal\my_custom_action\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Enable Premium Content.
 *
 * @Action(
 *   id = "add_premium_content",
 *   label = @Translation("Enable Premium Content"),
 *   type = "node"
 * )
 */
class AddPremiumContent extends ActionBase {

	/**
	 * {@inheritdoc}
	 */
	public function execute($entity = NULL) {
		/** @var \Drupal\node\NodeInterface $entity */
		if ($entity->hasField('field_premium_content')) {
			$entity->field_premium_content->value = 1;
			$entity->save();
		}

	}

	/**
	 * {@inheritdoc}
	 */
	public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
		/** @var \Drupal\node\NodeInterface $object */
		$result = $object->access('update', $account, TRUE)
			->andIf($object->field_premium_content->access('edit', $account, TRUE));

		return $return_as_object ? $result : $result->isAllowed();
	}

}
