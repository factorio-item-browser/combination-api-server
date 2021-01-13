<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Handler\Combination;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\CombinationApi\Client\Response\Combination\StatusResponse;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\CombinationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The handler for the status request.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class StatusHandler implements RequestHandlerInterface
{
    private CombinationService $combinationService;
    private MapperManagerInterface $mapperManager;

    public function __construct(CombinationService $combinationService, MapperManagerInterface $mapperManager)
    {
        $this->combinationService = $combinationService;
        $this->mapperManager = $mapperManager;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ServerException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $combination = $this->combinationService->getCombinationFromRequestHeader($request);
        $statusResponse = $this->mapperManager->map($combination, new StatusResponse());
        return new ClientResponse($statusResponse);
    }
}
