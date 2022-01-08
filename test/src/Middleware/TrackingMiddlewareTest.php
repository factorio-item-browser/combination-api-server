<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Middleware;

use BluePsyduck\Ga4MeasurementProtocol\Request\Event\EventInterface;
use FactorioItemBrowser\CombinationApi\Server\Middleware\TrackingMiddleware;
use FactorioItemBrowser\CombinationApi\Server\Service\TrackingService;
use FactorioItemBrowser\CombinationApi\Server\Tracking\Event\RequestEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The PHPUnit test of the ClientFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Middleware\TrackingMiddleware
 */
class TrackingMiddlewareTest extends TestCase
{
    /** @var TrackingService&MockObject */
    private TrackingService $trackingService;

    protected function setUp(): void
    {
        $this->trackingService = $this->createMock(TrackingService::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return TrackingMiddleware&MockObject
     */
    private function createInstance(array $mockedMethods = []): TrackingMiddleware
    {
        return $this->getMockBuilder(TrackingMiddleware::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->trackingService,
                    ])
                    ->getMock();
    }

    public function testProcess(): void
    {
        $statusCode = 200;
        $requestWithEvent = $this->createMock(ServerRequestInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->any())
                 ->method('getStatusCode')
                 ->willReturn($statusCode);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('withAttribute')
                ->with(
                    $this->identicalTo(RequestEvent::class),
                    $this->isInstanceOf(RequestEvent::class),
                )
                ->willReturn($requestWithEvent);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($requestWithEvent))
                ->willReturn($response);

        $this->trackingService->expects($this->once())
                              ->method('addEvent')
                              ->with($this->callback(function (EventInterface $event) use ($statusCode): bool {
                                  $this->assertInstanceOf(RequestEvent::class, $event);
                                  $this->assertSame($statusCode, $event->statusCode);
                                  $this->assertIsFloat($event->runtime);
                                  return true;
                              }));
        $this->trackingService->expects($this->once())
                              ->method('track');

        $instance = $this->createInstance();
        $result = $instance->process($request, $handler);

        $this->assertSame($response, $result);
    }
}
