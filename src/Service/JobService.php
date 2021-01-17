<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Service;

use Exception;
use FactorioItemBrowser\CombinationApi\Client\Constant\ParameterName;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidJobIdException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Exception\UnknownJobException;
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
    private JobRepository $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            $combinationId = null;
        }

        return $this->jobRepository->findAll(
            $combinationId,
            $queryParams[ParameterName::STATUS] ?? '',
            $queryParams[ParameterName::ORDER] ?? '',
            (int) ($queryParams[ParameterName::LIMIT] ?? 10)
        );
    }

    /**
     * Creates a new export job for the specified combination.
     * @param Combination $combination
     * @param string $priority
     * @return Job
     */
    public function createJobForCombination(Combination $combination, string $priority): Job
    {
        return $this->jobRepository->create($combination, 'test', $priority); // @todo replace initiator
    }

    /**
     * Changes the job to have the specified status and error message.
     * @param Job $job
     * @param string $status
     * @param string $errorMessage
     */
    public function changeJob(Job $job, string $status, string $errorMessage): void
    {
        $job->setErrorMessage($errorMessage);
        $this->jobRepository->addChange($job, 'test', $status); // @todo replace initiator
    }
}
