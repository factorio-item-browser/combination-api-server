<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Handler;

use FactorioItemBrowser\CombinationApi\Server\Exception\ApiEndpointNotFoundException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The handler throwing a 404 error as last instance.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class NotFoundHandler implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ServerException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        throw new ApiEndpointNotFoundException($request->getRequestTarget());
    }
}
