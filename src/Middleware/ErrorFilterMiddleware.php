<?php

namespace Beapp\Bugsnag\Ext\Middleware;

use Bugsnag\Report;
use ReflectionException;

/**
 * Add a way to ignore exceptions.
 * This is useful when exceptions are handled through a log aggregator and we don't want to pollute Bugsnag dashboard.
 */
class ErrorFilterMiddleware
{

    /** @var array<string> */
    private array $excludedExceptions;

    /**
     * @param array<string> $excludedExceptions
     */
    public function __construct(array $excludedExceptions = [])
    {
        $this->excludedExceptions = $excludedExceptions;
    }

    /**
     * @param Report $report the bugsnag report instance
     * @param callable $next the next stage callback
     * @return void
     * @throws ReflectionException
     */
    public function __invoke(Report $report, callable $next): void
    {
        foreach ($this->excludedExceptions as $excludedException) {
            if (is_a($report->getOriginalError(), $excludedException, true)) {
                return;
            }
        }

        $next($report);
    }

}
