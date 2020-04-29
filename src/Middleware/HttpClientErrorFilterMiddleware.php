<?php

namespace Beapp\Bugsnag\Ext\Middleware;

use Bugsnag\Report;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Add a way to ignore Http exceptions based on status code.
 * This is useful when http exceptions are handled through a log aggregator and we don't want to pollute Bugsnag dashboard.
 */
class HttpClientErrorFilterMiddleware
{

    /** @var integer[] */
    private $excludedHttpCodes;

    /**
     * @param array $excludedHttpCodes
     */
    public function __construct($excludedHttpCodes = [])
    {
        $this->excludedHttpCodes = $excludedHttpCodes;
    }

    /**
     * @param Report $report the bugsnag report instance
     * @param callable $next the next stage callback
     *
     * @return void
     */
    public function __invoke(Report $report, callable $next)
    {
        if (is_a($report->getOriginalError(), HttpException::class, true)) {
            /** @var HttpException $httpException */
            $httpException = $report->getOriginalError();

            foreach ($this->excludedHttpCodes as $excludedHttpCode) {
                if ($httpException->getStatusCode() === $excludedHttpCode) {
                    return;
                }
            }
        }

        $next($report);
    }


}
