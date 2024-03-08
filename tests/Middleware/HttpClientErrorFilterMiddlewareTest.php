<?php

namespace Beapp\Bugsnag\Ext\Middleware;

use Bugsnag\Report;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpClientErrorFilterMiddlewareTest extends TestCase
{

    public function testInvoke_noMatch()
    {
        $handledErrorMiddleware = new HttpClientErrorFilterMiddleware([401, 403]);

        $report = $this->createMock(Report::class);
        $report->expects($this->atLeast(1))->method('getOriginalError')->willReturn(new HttpException(500));

        $nextWasCalled = false;
        $next = function () use (&$nextWasCalled) {
            $nextWasCalled = true;
        };
        $handledErrorMiddleware($report, $next);
        self::assertTrue($nextWasCalled);
    }

    public function testInvoke_matchCodeExactInt()
    {
        $handledErrorMiddleware = new HttpClientErrorFilterMiddleware([401, 403]);

        $report = $this->createMock(Report::class);
        $report->expects($this->atLeast(1))->method('getOriginalError')->willReturn(new HttpException(401));

        $next = function () {
            self::fail('Next should not be called');
        };
        $handledErrorMiddleware($report, $next);
    }

    public function testInvoke_matchCodeExactString()
    {
        $handledErrorMiddleware = new HttpClientErrorFilterMiddleware(['401', '403']);

        $report = $this->createMock(Report::class);
        $report->expects($this->atLeast(1))->method('getOriginalError')->willReturn(new HttpException(401));

        $next = function () {
            self::fail('Next should not be called');
        };
        $handledErrorMiddleware($report, $next);
    }

    public function testInvoke_matchCodeWithPattern()
    {
        $handledErrorMiddleware = new HttpClientErrorFilterMiddleware(['4xx']);

        $report = $this->createMock(Report::class);
        $report->expects($this->atLeast(1))->method('getOriginalError')->willReturn(new HttpException(401));

        $next = function () {
            self::fail('Next should not be called');
        };
        $handledErrorMiddleware($report, $next);
    }
}
