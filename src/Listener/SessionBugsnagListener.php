<?php

namespace Beapp\Bugsnag\Ext\Listener;

use Bugsnag\Client;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SessionBugsnagListener implements EventSubscriberInterface
{

    private Client $client;
    private bool $sessionPerRequestEnabled;

    public function __construct(Client $client, bool $sessionPerRequestEnabled)
    {
        $this->client = $client;
        $this->sessionPerRequestEnabled = $sessionPerRequestEnabled;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->sessionPerRequestEnabled) {
            return;
        }

        $this->client->startSession();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$this->sessionPerRequestEnabled) {
            return;
        }

        $this->client->getSessionTracker()->sendSessions();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 256],
            KernelEvents::RESPONSE => ['onKernelResponse', 256]
        ];
    }

}
