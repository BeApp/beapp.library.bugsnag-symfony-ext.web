<?php

namespace Beapp\Bugsnag\Ext\Middleware;

use Bugsnag\Report;
use DomainException;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class HandledErrorMiddlewareTest extends TestCase
{

    public function testInvoke_noMatch(): void
    {
        $handledErrorMiddleware = new HandledErrorMiddleware([RuntimeException::class]);

        $report = $this->createMock(Report::class);
        $report->expects($this->once())->method('getOriginalError')->willReturn(new DomainException());
        $report->expects($this->never())->method('setUnhandled')->with(false);

        $next = function () {
        };
        $handledErrorMiddleware($report, $next);
    }

    public function testInvoke_matchExceptionParent(): void
    {
        $handledErrorMiddleware = new HandledErrorMiddleware([LogicException::class, RuntimeException::class]);

        $report = $this->createMock(Report::class);
        $report->expects($this->once())->method('getOriginalError')->willReturn(new DomainException());
        $report->expects($this->once())->method('setUnhandled')->with(false);

        $next = function () {
        };
        $handledErrorMiddleware($report, $next);
    }

    public function testInvoke_matchExceptionExact(): void
    {
        $handledErrorMiddleware = new HandledErrorMiddleware([LogicException::class, RuntimeException::class]);

        $report = $this->createMock(Report::class);
        $report->expects($this->once())->method('getOriginalError')->willReturn(new LogicException());
        $report->expects($this->once())->method('setUnhandled')->with(false);

        $next = function () {
        };
        $handledErrorMiddleware($report, $next);
    }

}
