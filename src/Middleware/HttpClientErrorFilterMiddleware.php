<?php

namespace Beapp\Bugsnag\Ext\Middleware;

use Bugsnag\Report;
use ReflectionAttribute;
use ReflectionException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Add a way to ignore Http exceptions based on status code.
 * This is useful when http exceptions are handled through a log aggregator and we don't want to pollute Bugsnag dashboard.
 */
class HttpClientErrorFilterMiddleware
{

    /** @var array<string|number> */
    private array $excludedHttpCodes;

    /**
     * @param array<string|number> $excludedHttpCodes
     */
    public function __construct(array $excludedHttpCodes = [])
    {
        $this->excludedHttpCodes = $excludedHttpCodes;
    }

    /**
     * @param Report $report the bugsnag report instance
     * @param callable $next the next stage callback
     * @return void
     * @throws ReflectionException
     */
    public function __invoke(Report $report, callable $next): void
    {
        $throwable = $report->getOriginalError();

        // Handle classic HttpException
        if (is_a($throwable, HttpException::class, true)) {
            /** @var HttpException $httpException */
            $httpException = $throwable;

            if ($this->shouldExclude($httpException->getStatusCode())) {
                return;
            }
        }

        // Handle exceptions with #[WithHttpStatus] attribute
        $class = new \ReflectionClass($throwable);
        do {
            if ($attributes = $class->getAttributes(WithHttpStatus::class, ReflectionAttribute::IS_INSTANCEOF)) {
                /** @var WithHttpStatus $instance */
                $instance = $attributes[0]->newInstance();

                if ($this->shouldExclude($instance->statusCode)) {
                    return;
                }
                break;
            }
        } while ($class = $class->getParentClass());

        $next($report);
    }

    private function shouldExclude(int $statusCode): bool
    {
        foreach ($this->excludedHttpCodes as $excludedHttpCode) {
            if (is_int($excludedHttpCode) && $statusCode === $excludedHttpCode) {
                return true;
            }
            if (is_string($excludedHttpCode) && $this->matchGlobStatus($statusCode, $excludedHttpCode)) {
                return true;
            }
        }

        return false;
    }

    private function matchGlobStatus(int $statusCode, string $globPattern): bool
    {
        $pattern = "#" . str_replace("x", "\\d", $globPattern) . "#";
        return preg_match($pattern, $statusCode);
    }

}
