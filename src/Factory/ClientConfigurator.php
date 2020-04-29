<?php

namespace Beapp\Bugsnag\Ext\Factory;

use Beapp\Bugsnag\Ext\Middleware\HandledErrorMiddleware;
use Beapp\Bugsnag\Ext\Middleware\HttpClientErrorFilterMiddleware;
use Bugsnag\Client;

class ClientConfigurator
{

    /** @var Client */
    private $bugsnagClient;

    /**
     * @param Client $bugsnagClient
     * @param HandledErrorMiddleware $handledErrorMiddleware
     * @param HttpClientErrorFilterMiddleware $httpClientErrorMiddleware
     * @param callable[] $extraMiddlewares
     */
    public function __construct($bugsnagClient, $handledErrorMiddleware, $httpClientErrorMiddleware, $extraMiddlewares)
    {
        $this->bugsnagClient = $bugsnagClient;

        $this->bugsnagClient->registerMiddleware($handledErrorMiddleware);
        $this->bugsnagClient->registerMiddleware($httpClientErrorMiddleware);
        foreach ($extraMiddlewares as $extraMiddleware) {
            $this->bugsnagClient->registerMiddleware($extraMiddleware);
        }
    }

}
