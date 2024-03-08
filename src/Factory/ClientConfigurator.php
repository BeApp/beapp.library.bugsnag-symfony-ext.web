<?php

namespace Beapp\Bugsnag\Ext\Factory;

use Beapp\Bugsnag\Ext\Middleware\HandledErrorMiddleware;
use Beapp\Bugsnag\Ext\Middleware\HttpClientErrorFilterMiddleware;
use Bugsnag\Client;

class ClientConfigurator
{

    private Client $bugsnagClient;

    /**
     * @param array<callable> $extraMiddlewares
     */
    public function __construct(Client                          $bugsnagClient,
                                HandledErrorMiddleware          $handledErrorMiddleware,
                                HttpClientErrorFilterMiddleware $httpClientErrorMiddleware,
                                array                           $extraMiddlewares)
    {
        $this->bugsnagClient = $bugsnagClient;

        $this->bugsnagClient->registerMiddleware($handledErrorMiddleware);
        $this->bugsnagClient->registerMiddleware($httpClientErrorMiddleware);
        foreach ($extraMiddlewares as $extraMiddleware) {
            $this->bugsnagClient->registerMiddleware($extraMiddleware);
        }
    }

}
