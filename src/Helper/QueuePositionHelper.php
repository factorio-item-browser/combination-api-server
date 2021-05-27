<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Helper;

use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use FactorioItemBrowser\CombinationApi\Client\Constant\ListOrder;
use FactorioItemBrowser\CombinationApi\Client\Transfer\Job;
use FactorioItemBrowser\CombinationApi\Server\Repository\JobRepository;

/**
 * The class helping with injecting the queue positions into the jobs.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class QueuePositionHelper
{
    private const MAX_QUEUE_POSITION = 10;

    private JobRepository $jobRepository;
    /** @var array<string, int> */
    private array $queuePositions;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    /**
     * Injects the queue position into the specified job.
     * @param Job $job
     */
    public function injectQueuePosition(Job $job): void
    {
        if ($job->status === JobStatus::QUEUED) {
            if (!isset($this->queuePositions)) {
                $this->queuePositions = $this->fetchQueuePositions();
            }

            $job->queuePosition = $this->queuePositions[$job->id] ?? self::MAX_QUEUE_POSITION;
        }
    }

    /**
     * @return array<string, int>
     */
    private function fetchQueuePositions(): array
    {
        $positions = [];
        $jobs = $this->jobRepository->findAll(
            null,
            JobStatus::QUEUED,
            ListOrder::PRIORITY,
            self::MAX_QUEUE_POSITION,
            0,
        );
        foreach ($jobs as $index => $job) {
            $positions[$job->getId()->toString()] = $index + 1;
        }
        return $positions;
    }
}
