<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Repository;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use FactorioItemBrowser\CombinationApi\Client\Constant\ListOrder;
use FactorioItemBrowser\CombinationApi\Server\Doctrine\Type\JobStatusType;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Entity\JobChange;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository for the jobs and their changes.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class JobRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Finds the job with the specified id.
     * @param UuidInterface $jobId
     * @return Job|null
     */
    public function findById(UuidInterface $jobId): ?Job
    {
        $entity = Job::class;
        $query = $this->entityManager->createQuery(
            "SELECT j, c FROM {$entity} j LEFT JOIN j.changes c WHERE j.id = :jobId"
        );
        $query->setParameter('jobId', $jobId, UuidBinaryType::NAME);
        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // Will never happen: We are searching for the primary key.
            return null;
        }
    }

    /**
     * Finds all jobs matching the specified parameters.
     * @param UuidInterface|null $combinationId
     * @param string $status
     * @param string $order
     * @param int $limit
     * @return array<Job>
     */
    public function findAll(?UuidInterface $combinationId, string $status, string $order, int $limit): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('j', 'c')
                     ->from(Job::class, 'j')
                     ->leftJoin('j.changes', 'c')
                     ->leftJoin('j.changes', 'c2', 'WITH', 'c2.status = :statusQueued')
                     ->setParameter('statusQueued', JobStatus::QUEUED)
                     ->setMaxResults($limit);

        if ($combinationId !== null) {
            $queryBuilder->andWhere('j.combination = :combinationId')
                         ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME);
        }
        if ($status !== '') {
            $queryBuilder->andWhere('j.status = :status')
                         ->setParameter('status', $status, JobStatusType::NAME);
        }

        switch ($order) {
            case ListOrder::PRIORITY:
                $queryBuilder->addOrderBy('j.priority', 'ASC')
                             ->addOrderBy('c2.timestamp', 'ASC')
                             ->addOrderBy('j.id', 'ASC');
                break;

            case ListOrder::LATEST:
                $queryBuilder->addOrderBy('c2.timestamp', 'DESC')
                             ->addOrderBy('j.id', 'ASC');
                break;

            case ListOrder::CREATION:
            default:
                $queryBuilder->addOrderBy('c2.timestamp', 'ASC')
                             ->addOrderBy('j.id', 'ASC');
                break;
        }


        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Creates a new job in the database.
     * @param Combination $combination
     * @param string $initiator
     * @param string $priority
     * @return Job
     */
    public function create(Combination $combination, string $initiator, string $priority): Job
    {
        $job = new Job();
        $job->setCombination($combination)
            ->setStatus(JobStatus::QUEUED)
            ->setPriority($priority)
            ->setErrorMessage('');

        $this->entityManager->persist($job);
        $this->entityManager->flush();

        $this->addChange($job, $initiator, JobStatus::QUEUED);
        return $job;
    }

    /**
     * Adds a change to the job.
     * @param Job $job
     * @param string $initiator
     * @param string $status
     */
    public function addChange(Job $job, string $initiator, string $status): void
    {
        $change = new JobChange();
        $change->setJob($job)
               ->setInitiator($initiator)
               ->setStatus($status)
               ->setTimestamp(new DateTimeImmutable());

        $job->setStatus($status);
        $job->getChanges()->add($change);

        $this->entityManager->persist($job);
        $this->entityManager->persist($change);
        $this->entityManager->flush();
    }
}