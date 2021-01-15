<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Service;

use Exception;
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
}
