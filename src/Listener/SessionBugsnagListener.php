<?php

namespace Beapp\Bugsnag\Ext\Listener;

use Bugsnag\Client;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SessionBugsnagListener implements EventSubscriberInterface
{

    /** @var Client */
    private $client;
    /** @var bool */
    private $sessionPerRequestEnabled;

    /**
     * @param Client $client
     * @param bool $sessionPerRequestEnabled
     */
    public function __construct(Client $client, $sessionPerRequestEnabled)
    {
        $this->client = $client;
        $this->sessionPerRequestEnabled = $sessionPerRequestEnabled;
    }

    public function onKernelRequest($event)
    {
        if (!$this->sessionPerRequestEnabled) {
            return;
        }

        // Compatibility with Symfony < 5 and Symfony >=5
        if (!$event instanceof GetResponseEvent && !$event instanceof RequestEvent) {
            throw new InvalidArgumentException('onKernelRequest function only accepts GetResponseEvent and RequestEvent arguments');
        }

        $this->client->startSession();
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$this->sessionPerRequestEnabled) {
            return;
        }

//        $this->client->getSessionTracker()->sendSessions();
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 256],
            KernelEvents::RESPONSE => ['onKernelResponse', 256]
        ];
    }

}
