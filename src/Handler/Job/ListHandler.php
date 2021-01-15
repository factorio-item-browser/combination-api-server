<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Handler\Job;

use BluePsyduck\MapperManager\MapperManagerInterface;
use Exception;
use FactorioItemBrowser\CombinationApi\Client\Constant\ParameterName;
use FactorioItemBrowser\CombinationApi\Client\Response\Job\ListResponse;
use FactorioItemBrowser\CombinationApi\Client\Transfer\Job as ClientJob;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job as DatabaseJob;
use FactorioItemBrowser\CombinationApi\Server\Repository\JobRepository;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

/**
 * The handler for the job list request.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ListHandler implements RequestHandlerInterface
{
    private JobRepository $jobRepository;
    private MapperManagerInterface $mapperManager;

    public function __construct(JobRepository $jobRepository, MapperManagerInterface $mapperManager)
    {
        $this->jobRepository = $jobRepository;
        $this->mapperManager = $mapperManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        try {
            $combinationId = Uuid::fromString($queryParams[ParameterName::COMBINATION_ID] ?? '');
        } catch (Exception $e) {
            $combinationId = null;
        }

        $jobs = $this->jobRepository->findAll(
            $combinationId,
            $queryParams[ParameterName::STATUS] ?? '',
            $queryParams[ParameterName::ORDER] ?? '',
            (int) ($queryParams[ParameterName::LIMIT] ?? 10)
        );

        $listResponse = new ListResponse();
        $listResponse->jobs = array_map(function (DatabaseJob $job): ClientJob {
            return $this->mapperManager->map($job, new ClientJob());
        }, $jobs);
        return new ClientResponse($listResponse);
    }
}
