<?php

namespace Beapp\Bugsnag\Ext\Middleware;

use Bugsnag\Report;

/**
 * Used to force specific classes as handled exceptions to prevent impacting Bugsnag's Stability score.
 * This is useful with exceptions properly handled with Normalizer, for example.
 */
class HandledErrorMiddleware
{

    /** @var array<string> */
    private array $handledExceptions;

    /**
     * @param array<string> $handledExceptions Classname of exceptions to consider as handled, despite being thrown (usually handle with a Normalizer for API)
     */
    public function __construct(array $handledExceptions = [])
    {
        $this->handledExceptions = $handledExceptions;
    }

    /**
     * @param Report $report the bugsnag report instance
     * @param callable $next the next stage callback
     * @return void
     */
    public function __invoke(Report $report, callable $next): void
    {
        foreach ($this->handledExceptions as $handledException) {
            if (is_a($report->getOriginalError(), $handledException, true)) {
                $report->setUnhandled(false);
                break;
            }
        }

        $next($report);
    }


}
