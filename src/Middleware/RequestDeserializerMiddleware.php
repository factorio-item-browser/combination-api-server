<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Middleware;

use Exception;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidRequestBodyException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use JMS\Serializer\SerializerInterface;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The middleware deserializing the request body if required.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RequestDeserializerMiddleware implements MiddlewareInterface
{
    private SerializerInterface $combinationApiClientSerializer;

    /** @var array<string, class-string<object>> */
    private array $requestClassesByRoutes;

    /**
     * @param SerializerInterface $combinationApiClientSerializer
     * @param array<string, class-string<object>> $requestClassesByRoutes
     */
    public function __construct(SerializerInterface $combinationApiClientSerializer, array $requestClassesByRoutes)
    {
        $this->combinationApiClientSerializer = $combinationApiClientSerializer;
        $this->requestClassesByRoutes = $requestClassesByRoutes;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ServerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);
        $requestClass = $this->requestClassesByRoutes[$routeResult->getMatchedRouteName()] ?? '';

        if ($request->getHeaderLine('Content-Type') === 'application/json' && $requestClass !== '') {
            try {
                $clientRequest = $this->combinationApiClientSerializer->deserialize(
                    $request->getBody()->getContents(),
                    $requestClass,
                    'json',
                );
            } catch (Exception $e) {
                throw new InvalidRequestBodyException($e->getMessage(), $e);
            }

            $request = $request->withParsedBody($clientRequest);
        }

        return $handler->handle($request);
    }
}
