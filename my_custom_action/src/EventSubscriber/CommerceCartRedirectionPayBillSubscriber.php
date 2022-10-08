<?php

namespace Drupal\my_custom_action\EventSubscriber;

use Drupal\commerce_cart\Event\CartEntityAddEvent;
use Drupal\commerce_cart\Event\CartEvents;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Drupal\Component\Utility\UrlHelper;

class CommerceCartRedirectionPayBillSubscriber implements EventSubscriberInterface {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The route provider to load routes by name.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * CartEventSubscriber constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   The route provider.
   */
  public function __construct(RequestStack $request_stack, RouteProviderInterface $route_provider) {
    $this->requestStack = $request_stack;
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      CartEvents::CART_ENTITY_ADD => 'tryPayBillRedirect',
      KernelEvents::RESPONSE => ['checkRedirectIssued', -9],
    ];
    return $events;
  }

  /**
   * Conditionally skip cart and send user to checkout if the order has pay bill product variation.
   *
   * 
   *
   * @param \Drupal\commerce_cart\Event\CartEntityAddEvent $event
   *   The add to cart event.
   */
  public function tryPayBillRedirect(CartEntityAddEvent $event) {
    $redirect = FALSE;
    $purchased_entity = $event->getEntity();
    if($purchased_entity->bundle() == 'pay_bill') {
      $redirect = TRUE;
    }
    if ($redirect) {
      $redirection_url = Url::fromRoute('commerce_checkout.form', [
        'commerce_order' => $event->getCart()->id(),
      ])->toString();
      $this->requestStack->getCurrentRequest()->attributes
        ->set('commerce_cart_redirection_url', $redirection_url);
    }
  }

  /**
   * Checks if a redirect url has been set.
   *
   * Redirects to the provided url if there is one.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   The response event.
   */
  public function checkRedirectIssued(FilterResponseEvent $event) {
    $request = $event->getRequest();
    $redirect_url = $request->attributes->get('commerce_cart_redirection_url');
    if (isset($redirect_url)) {
      $event->setResponse(new RedirectResponse($redirect_url));
    }
  }

}
