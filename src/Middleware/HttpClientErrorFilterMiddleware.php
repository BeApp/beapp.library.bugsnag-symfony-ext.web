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

            if ($this->shouldExclude($httpException->getStatusCode())) {
                return;
            }
        }

        $next($report);
    }

    /**
     * @param int $statusCode
     * @return bool
     */
    private function shouldExclude($statusCode)
    {
        foreach ($this->excludedHttpCodes as $excludedHttpCode) {
            if (is_int($excludedHttpCode) && $statusCode === $excludedHttpCode) {
                return true;
            } else if (is_string($excludedHttpCode) && $this->matchGlobStatus($statusCode, $excludedHttpCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $statusCode
     * @param string $globPattern
     * @return bool
     */
    private function matchGlobStatus($statusCode, $globPattern)
    {
        $pattern = "#" . str_replace("x", "\\d", $globPattern) . "#";
        return preg_match($pattern, $statusCode);
    }

}
