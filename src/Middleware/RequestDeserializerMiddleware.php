<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Middleware;

use BluePsyduck\LaminasAutoWireFactory\Attribute\Alias;
use BluePsyduck\LaminasAutoWireFactory\Attribute\ReadConfig;
use Exception;
use FactorioItemBrowser\CombinationApi\Client\Constant\ServiceName;
use FactorioItemBrowser\CombinationApi\Server\Constant\ConfigKey;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidRequestBodyException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Tracking\Event\RequestEvent;
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
    /**
     * @param SerializerInterface $combinationApiClientSerializer
     * @param array<string, class-string<object>> $requestClassesByRoutes
     */
    public function __construct(
        #[Alias(ServiceName::SERIALIZER)]
        private readonly SerializerInterface $combinationApiClientSerializer,
        #[ReadConfig(ConfigKey::MAIN, ConfigKey::REQUEST_CLASSES_BY_ROUTES)]
        private readonly array $requestClassesByRoutes,
    ) {
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

        /** @var RequestEvent $trackingRequestEvent */
        $trackingRequestEvent = $request->getAttribute(RequestEvent::class);
        $trackingRequestEvent->routeName = (string) $routeResult->getMatchedRouteName();

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
