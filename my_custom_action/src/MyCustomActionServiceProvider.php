<?php

namespace Drupal\my_custom_action;


use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;

// @note: You only need Reference, if you want to change service arguments.
use Symfony\Component\DependencyInjection\Reference;



/**
 * Modifies the language manager service.
 */
class MyCustomActionServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    return '';
    //kint($container->getDefinition());
    // Overrides language_manager class to test domain language negotiation.
    // Adds entity_type.manager service as an additional argument.
     //$definition = $container->getDefinition('commerce_license.license_availability_checker_existing');
     //kint($definition);
     //exit;
     //$definition->setClass('Drupal\my_custom_action\LicenseAvailabilityCheckerExistingRightsByCustom');
    //   ->addArgument(new Reference('entity_type.manager'));
  }
}