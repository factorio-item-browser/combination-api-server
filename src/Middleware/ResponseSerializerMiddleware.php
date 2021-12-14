<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Middleware;

use BluePsyduck\LaminasAutoWireFactory\Attribute\Alias;
use FactorioItemBrowser\CombinationApi\Client\Constant\ServiceName;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The middleware serializing the client response.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ResponseSerializerMiddleware implements MiddlewareInterface
{
    public function __construct(
        #[Alias(ServiceName::SERIALIZER)]
        private readonly SerializerInterface $combinationApiClientSerializer,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($response instanceof ClientResponse) {
            $response = $response->withSerializer($this->combinationApiClientSerializer);
        }
        return $response;
    }
}
