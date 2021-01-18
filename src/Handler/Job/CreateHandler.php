<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Handler\Job;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\CombinationApi\Client\Request\Job\CreateRequest;
use FactorioItemBrowser\CombinationApi\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\CombinationService;
use FactorioItemBrowser\CombinationApi\Server\Service\JobService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 *  The handler for creating a new export job.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CreateHandler implements RequestHandlerInterface
{
    private CombinationService $combinationService;
    private JobService $jobService;
    private MapperManagerInterface $mapperManager;

    public function __construct(
        CombinationService $combinationService,
        JobService $jobService,
        MapperManagerInterface $mapperManager
    ) {
        $this->combinationService = $combinationService;
        $this->jobService = $jobService;
        $this->mapperManager = $mapperManager;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ServerException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var CreateRequest $clientRequest */
        $clientRequest = $request->getParsedBody();

        $combination = $this->combinationService->getCombinationFromRequestValue($clientRequest->combinationId);

        $job = $combination->getUnfinishedJob();
        if ($job === null) {
            $job = $this->jobService->createJobForCombination($combination, $clientRequest->priority);
        }
        return new ClientResponse($this->mapperManager->map($job, new DetailsResponse()));
    }
}
