<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Handler\Job;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\CombinationApi\Client\Request\Job\UpdateRequest;
use FactorioItemBrowser\CombinationApi\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\JobService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The handler for the job update request.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class UpdateHandler implements RequestHandlerInterface
{
    private JobService $jobService;
    private MapperManagerInterface $mapperManager;

    public function __construct(
        JobService $jobService,
        MapperManagerInterface $mapperManager
    ) {
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
        /** @var UpdateRequest $clientRequest */
        $clientRequest = $request->getParsedBody();

        $job = $this->jobService->getJobFromRequestValue($request->getAttribute('job-id'));
        $this->jobService->changeJob($job, $clientRequest->status, $clientRequest->errorMessage);
        return new ClientResponse($this->mapperManager->map($job, new DetailsResponse()));
    }
}
