<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Middleware;

use FactorioItemBrowser\CombinationApi\Server\Exception\ApiEndpointNotFoundException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The middleware checking whether we are actually able to route the request.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RejectUnknownRoutesMiddleware implements MiddlewareInterface
{
    /**
     * @throws ServerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        if (!$routeResult instanceof RouteResult || !$routeResult->isSuccess()) {
            throw new ApiEndpointNotFoundException($request->getRequestTarget());
        }

        return $handler->handle($request);
    }
}
