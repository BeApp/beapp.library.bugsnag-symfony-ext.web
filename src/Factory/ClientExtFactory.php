<?php

namespace Beapp\Bugsnag\Ext\Factory;

use Beapp\Bugsnag\Ext\Middleware\HandledErrorMiddleware;
use Beapp\Bugsnag\Ext\Middleware\HttpClientErrorFilterMiddleware;
use Bugsnag\BugsnagBundle\DependencyInjection\ClientFactory;

class ClientExtFactory
{

    /** @var ClientFactory */
    private $bugsnagClientFactory;
    /** @var HandledErrorMiddleware */
    private $handledErrorMiddleware;
    /** @var HttpClientErrorFilterMiddleware */
    private $httpClientErrorMiddleware;

    /**
     * @param ClientFactory $bugsnagClientFactory
     * @param HandledErrorMiddleware $handledErrorMiddleware
     * @param HttpClientErrorFilterMiddleware $httpClientErrorMiddleware
     */
    public function __construct($bugsnagClientFactory, $handledErrorMiddleware, $httpClientErrorMiddleware)
    {
        $this->bugsnagClientFactory = $bugsnagClientFactory;
        $this->handledErrorMiddleware = $handledErrorMiddleware;
        $this->httpClientErrorMiddleware = $httpClientErrorMiddleware;
    }

    /**
     * Make a new client instance.
     *
     * @return \Bugsnag\Client
     */
    public function make()
    {
        $client = $this->bugsnagClientFactory->make();

        $client->registerMiddleware($this->handledErrorMiddleware);
        $client->registerMiddleware($this->httpClientErrorMiddleware);

        return $client;
    }

}
