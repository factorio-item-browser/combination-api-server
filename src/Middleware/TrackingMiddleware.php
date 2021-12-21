<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Middleware;

use FactorioItemBrowser\CombinationApi\Server\Service\TrackingService;
use FactorioItemBrowser\CombinationApi\Server\Tracking\Event\RequestEvent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The middleware handling the sending of the tracked data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TrackingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly TrackingService $trackingService,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $startTime = microtime(true);
        $trackingRequestEvent = new RequestEvent();

        $trackingRequestEvent->agentName = 'anonymous';
        $trackingRequestEvent->routeName = 'unknown';

        $request = $request->withAttribute(RequestEvent::class, $trackingRequestEvent);
        $response = $handler->handle($request);

        $trackingRequestEvent->runtime = round((microtime(true) - $startTime) * 1000);
        $trackingRequestEvent->statusCode = $response->getStatusCode();

        $this->trackingService->addEvent($trackingRequestEvent);
        $this->trackingService->track();

        return $response;
    }
}
