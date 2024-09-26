<?php

namespace Beapp\Bugsnag\Ext\Middleware;

use Bugsnag\Report;
use DomainException;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ErrorFilterMiddlewareTest extends TestCase
{

    public function testInvoke_noMatch(): void
    {
        $errorFilterMiddleware = new ErrorFilterMiddleware([RuntimeException::class]);

        $report = $this->createMock(Report::class);
        $report->expects($this->once())->method('getOriginalError')->willReturn(new DomainException());

        $next = function () {
            self::assertTrue(true);
        };
        $errorFilterMiddleware($report, $next);
    }

    public function testInvoke_matchExceptionParent(): void
    {
        $errorFilterMiddleware = new ErrorFilterMiddleware([LogicException::class, RuntimeException::class]);

        $report = $this->createMock(Report::class);
        $report->expects($this->once())->method('getOriginalError')->willReturn(new DomainException());

        $next = function () {
            self::fail("Shouldn't call next");
        };
        $errorFilterMiddleware($report, $next);
    }

    public function testInvoke_matchExceptionExact(): void
    {
        $errorFilterMiddleware = new ErrorFilterMiddleware([LogicException::class, RuntimeException::class]);

        $report = $this->createMock(Report::class);
        $report->expects($this->once())->method('getOriginalError')->willReturn(new LogicException());

        $next = function () {
            self::fail("Shouldn't call next");
        };
        $errorFilterMiddleware($report, $next);
    }

}
