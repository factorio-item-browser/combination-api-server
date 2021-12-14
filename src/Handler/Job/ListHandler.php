<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Handler\Job;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\CombinationApi\Client\Response\Job\ListResponse;
use FactorioItemBrowser\CombinationApi\Client\Transfer\Job as ClientJob;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job as DatabaseJob;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\JobService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The handler for the job list request.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ListHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly JobService $jobService,
        private readonly MapperManagerInterface $mapperManager,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $jobs = $this->jobService->getJobsFromQueryParams($request->getQueryParams());

        $listResponse = new ListResponse();
        $listResponse->jobs = array_map(function (DatabaseJob $job): ClientJob {
            return $this->mapperManager->map($job, new ClientJob());
        }, $jobs);
        return new ClientResponse($listResponse);
    }
}
