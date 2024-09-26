<?php

namespace Beapp\Bugsnag\Ext\Factory;

use Beapp\Bugsnag\Ext\Middleware\ErrorFilterMiddleware;
use Beapp\Bugsnag\Ext\Middleware\HandledErrorMiddleware;
use Beapp\Bugsnag\Ext\Middleware\HttpClientErrorFilterMiddleware;
use Bugsnag\BugsnagBundle\DependencyInjection\ClientFactory;
use Bugsnag\Client;

class ClientExtFactory
{
    private ClientFactory $bugsnagClientFactory;
    private HandledErrorMiddleware $handledErrorMiddleware;
    private ErrorFilterMiddleware $errorFilterMiddleware;
    private HttpClientErrorFilterMiddleware $httpClientErrorMiddleware;

    public function __construct(
        ClientFactory                   $bugsnagClientFactory,
        HandledErrorMiddleware          $handledErrorMiddleware,
        ErrorFilterMiddleware           $errorFilterMiddleware,
        HttpClientErrorFilterMiddleware $httpClientErrorMiddleware
    )
    {
        $this->bugsnagClientFactory = $bugsnagClientFactory;
        $this->handledErrorMiddleware = $handledErrorMiddleware;
        $this->errorFilterMiddleware = $errorFilterMiddleware;
        $this->httpClientErrorMiddleware = $httpClientErrorMiddleware;
    }

    public function make(): Client
    {
        $client = $this->bugsnagClientFactory->make();

        $client->registerMiddleware($this->handledErrorMiddleware);
        $client->registerMiddleware($this->errorFilterMiddleware);
        $client->registerMiddleware($this->httpClientErrorMiddleware);

        return $client;
    }

}
