<?php

namespace Drupal\my_custom_action\Plugin\Field\FieldWidget;

use Drupal\commerce\InlineFormManager;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of 'commerce_delivery_address_profile'.
 *
 * @FieldWidget(
 *   id = "commerce_delivery_address_profile",
 *   label = @Translation("Delivery Address"),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
class CustomerDeliveryAddressProfileWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The inline form manager.
   *
   * @var \Drupal\commerce\InlineFormManager
   */
  protected $inlineFormManager;

  /**
   * Constructs a new BillingProfileWidget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce\InlineFormManager $inline_form_manager
   *   The inline form manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager, InlineFormManager $inline_form_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);

    $this->entityTypeManager = $entity_type_manager;
    $this->inlineFormManager = $inline_form_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.commerce_inline_form')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $items[$delta]->getEntity();
    $store = $order->getStore();
    if(count($order->get('field_delivery_address')->getVAlue()) && isset($order->get('field_delivery_address')->getVAlue()[0]['target_id'])) {
      /** @var \Drupal\profile\Entity\ProfileInterface $profile */
      if (!$items[$delta]->isEmpty() && $items[$delta]->entity) {
        $profile = $items[$delta]->entity;
      }
      else {
        $profile = $this->entityTypeManager->getStorage('profile')->create([
          'type' => 'customer_delivery_addresses',
          'uid' => 0,
        ]);
      }
      $inline_form = $this->inlineFormManager->createInstance('customer_delivery_address', [
        'profile_scope' => 'delivery_address',
        'available_countries' => $store->getBillingCountries(),
        'address_book_uid' => $order->getCustomerId(),
        'admin' => TRUE,
      ], $profile);

      $element['#type'] = 'fieldset';
      $element['profile'] = [
        '#parents' => array_merge($element['#field_parents'], [$items->getName(), $delta, 'profile']),
        '#inline_form' => $inline_form,
      ];
      $element['profile'] = $inline_form->buildInlineForm($element['profile'], $form_state);

      // Workaround for massageFormValues() not getting $element.
      $element['array_parents'] = [
        '#type' => 'value',
        '#value' => array_merge($element['#field_parents'], [$items->getName(), 'widget', $delta]),
      ];
    }
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $new_values = [];
    foreach ($values as $delta => $value) {
      $element = NestedArray::getValue($form, $value['array_parents']);
      /** @var \Drupal\commerce\Plugin\Commerce\InlineForm\EntityInlineFormInterface $inline_form */
      $inline_form = $element['profile']['#inline_form'];
      $new_values[$delta]['entity'] = $inline_form->getEntity();
    }
    return $new_values;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    $entity_type = $field_definition->getTargetEntityTypeId();
    $field_name = $field_definition->getName();
    return $entity_type == 'commerce_order' && $field_name == 'field_delivery_address';
  }

}
