<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Service;

use DateTimeImmutable;
use Exception;
use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use FactorioItemBrowser\CombinationApi\Client\Constant\ParameterName;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Exception\ActionNotAllowedException;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidJobIdException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Exception\UnknownJobException;
use FactorioItemBrowser\CombinationApi\Server\Repository\AgentRepository;
use FactorioItemBrowser\CombinationApi\Server\Repository\JobRepository;
use Ramsey\Uuid\Uuid;

/**
 * The service handling the jobs.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class JobService
{
    public function __construct(
        private readonly AgentRepository $agentRepository,
        private readonly JobRepository $jobRepository,
    ) {
    }

    /**
     * Returns the job from the id specified in the request.
     * @param string $jobId
     * @return Job
     * @throws ServerException
     */
    public function getJobFromRequestValue(string $jobId): Job
    {
        try {
            $id = Uuid::fromString($jobId);
        } catch (Exception) {
            throw new InvalidJobIdException($jobId);
        }

        $job = $this->jobRepository->findById($id);
        if ($job === null) {
            throw new UnknownJobException($id);
        }
        return $job;
    }

    /**
     * Returns the jobs matching the search criteria from the specified query parameters.
     * @param array<string, string> $queryParams
     * @return array<Job>
     */
    public function getJobsFromQueryParams(array $queryParams): array
    {
        try {
            $combinationId = Uuid::fromString($queryParams[ParameterName::COMBINATION_ID] ?? '');
        } catch (Exception) {
            $combinationId = null;
        }

        return $this->jobRepository->findAll(
            $combinationId,
            $queryParams[ParameterName::STATUS] ?? '',
            $queryParams[ParameterName::ORDER] ?? '',
            (int) ($queryParams[ParameterName::LIMIT] ?? 10),
            (int) ($queryParams[ParameterName::FIRST] ?? 0),
        );
    }

    /**
     * Creates a new export job for the specified combination.
     * @param Combination $combination
     * @param string $priority
     * @return Job
     * @throws ServerException
     */
    public function createJobForCombination(Combination $combination, string $priority): Job
    {
        $agent = $this->agentRepository->getCurrentAgent();
        if (!$agent->getCanCreateJobs()) {
            throw new ActionNotAllowedException();
        }

        return $this->jobRepository->create($combination, $agent->getName(), $priority);
    }

    /**
     * Changes the job to have the specified status and error message.
     * @param Job $job
     * @param string $status
     * @param string $errorMessage
     * @throws ServerException
     */
    public function changeJob(Job $job, string $status, string $errorMessage): void
    {
        $agent = $this->agentRepository->getCurrentAgent();
        if (!$agent->getCanUpdateJobs()) {
            throw new ActionNotAllowedException();
        }

        $job->setErrorMessage($errorMessage);
        if ($status === JobStatus::DONE) {
            $job->getCombination()->setExportTime(new DateTimeImmutable());
        }

        $this->jobRepository->addChange($job, $agent->getName(), $status);
    }
}
