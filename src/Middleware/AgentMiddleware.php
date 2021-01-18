<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Middleware;

use FactorioItemBrowser\CombinationApi\Client\Constant\HeaderName;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidApiKeyException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Repository\AgentRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The middleware for detecting the agent.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class AgentMiddleware implements MiddlewareInterface
{
    private AgentRepository $agentRepository;

    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ServerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $agent = null;
        $apiKey = $request->getHeaderLine(HeaderName::API_KEY);
        if ($apiKey !== '') {
            $agent = $this->agentRepository->findByApiKey($apiKey);
        }
        if ($agent === null) {
            throw new InvalidApiKeyException();
        }

        $this->agentRepository->setCurrentAgent($agent);
        return $handler->handle($request);
    }
}
