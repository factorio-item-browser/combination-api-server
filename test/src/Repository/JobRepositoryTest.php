<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use FactorioItemBrowser\CombinationApi\Client\Constant\ListOrder;
use FactorioItemBrowser\CombinationApi\Server\Doctrine\Type\JobStatusType;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Entity\JobChange;
use FactorioItemBrowser\CombinationApi\Server\Repository\JobRepository;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the JobRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Repository\JobRepository
 */
class JobRepositoryTest extends TestCase
{
    public function testFindById(): void
    {
        $jobId = $this->createMock(UuidInterface::class);
        $job = $this->createMock(Job::class);

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('setParameter')
              ->with(
                  $this->identicalTo('jobId'),
                  $this->identicalTo($jobId),
                  $this->identicalTo(UuidBinaryType::NAME),
              );
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willReturn($job);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
                      ->method('createQuery')
                      ->with($this->isType('string'))
                      ->willReturn($query);

        $instance = new JobRepository($entityManager);
        $result = $instance->findById($jobId);

        $this->assertSame($job, $result);
    }

    public function testFindByIdWithException(): void
    {
        $jobId = $this->createMock(UuidInterface::class);

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('setParameter')
              ->with(
                  $this->identicalTo('jobId'),
                  $this->identicalTo($jobId),
                  $this->identicalTo(UuidBinaryType::NAME),
              );
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willThrowException($this->createMock(NonUniqueResultException::class));

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
                      ->method('createQuery')
                      ->with($this->isType('string'))
                      ->willReturn($query);

        $instance = new JobRepository($entityManager);
        $result = $instance->findById($jobId);

        $this->assertNull($result);
    }

    /**
     * @return array<mixed>
     */
    public function provideFindAll(): array
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        return [
            [
                $combinationId,
                'abc',
                ListOrder::CREATION,
                [
                    ['j.combination = :combinationId'],
                    ['j.status = :status'],
                ],
                [
                    ['combinationId', $combinationId, UuidBinaryType::NAME],
                    ['status', 'abc', JobStatusType::NAME],
                ],
                [
                    ['j.creationTime', 'ASC'],
                    ['j.id', 'ASC'],
                ],
            ],
            [
                null,
                '',
                ListOrder::PRIORITY,
                [],
                [],
                [
                    ['j.priority', 'ASC'],
                    ['j.creationTime', 'ASC'],
                    ['j.id', 'ASC'],
                ],
            ],
            [
                null,
                '',
                ListOrder::LATEST,
                [],
                [],
                [
                    ['j.creationTime', 'DESC'],
                    ['j.id', 'ASC'],
                ],
            ],
        ];
    }

    /**
     * @param UuidInterface|null $combinationId
     * @param string $status
     * @param string $order
     * @param array<mixed> $expectedConditions
     * @param array<mixed> $expectedParameters
     * @param array<mixed> $expectedOrders
     * @dataProvider provideFindAll
     */
    public function testFindAll(
        ?UuidInterface $combinationId,
        string $status,
        string $order,
        array $expectedConditions,
        array $expectedParameters,
        array $expectedOrders
    ): void {
        $limit = 42;

        $jobs = [
            $this->createMock(Job::class),
            $this->createMock(Job::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($jobs);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('j'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Job::class), $this->identicalTo('j'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(count($expectedConditions)))
                     ->method('andWhere')
                     ->withConsecutive(...$expectedConditions)
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(count($expectedParameters)))
                     ->method('setParameter')
                     ->withConsecutive(...$expectedParameters)
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setMaxResults')
                     ->with($this->identicalTo($limit))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(count($expectedOrders)))
                     ->method('addOrderBy')
                     ->withConsecutive(...$expectedOrders)
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $instance = new JobRepository($entityManager);
        $result = $instance->findAll($combinationId, $status, $order, $limit);

        $this->assertSame($jobs, $result);
    }

    public function testCreate(): void
    {
        $initiator = 'abc';
        $priority = 'def';
        $combination = $this->createMock(Combination::class);

        $expectedJob = new Job();
        $expectedJob->setCombination($combination)
                    ->setPriority($priority)
                    ->setErrorMessage('');

        $expectedChange = new JobChange();
        $expectedChange->setInitiator($initiator)
                       ->setStatus(JobStatus::QUEUED);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(2))
                      ->method('persist')
                      ->withConsecutive(
                          [new Callback(function (Job $actualJob) use ($expectedJob): bool {
                              $this->assertSame($expectedJob->getCombination(), $actualJob->getCombination());
                              $this->assertSame($expectedJob->getPriority(), $actualJob->getPriority());
                              $this->assertSame($expectedJob->getErrorMessage(), $actualJob->getErrorMessage());
                              return true;
                          })],
                          [new Callback(function (JobChange $actualChange) use ($expectedChange): bool {
                              $this->assertSame($expectedChange->getInitiator(), $actualChange->getInitiator());
                              $this->assertSame($expectedChange->getStatus(), $actualChange->getStatus());
                              return true;
                          })],
                      );
        $entityManager->expects($this->once())
                      ->method('flush');

        $instance = new JobRepository($entityManager);
        $instance->create($combination, $initiator, $priority);
    }

    public function testAddChange(): void
    {
        $initiator = 'abc';
        $status = 'def';
        $job = $this->createMock(Job::class);

        $expectedChange = new JobChange();
        $expectedChange->setJob($job)
                       ->setInitiator($initiator)
                       ->setStatus($status);

        $changes = $this->createMock(Collection::class);
        $changes->expects($this->once())
                ->method('add')
                ->with(new Callback(function (JobChange $actualChange) use ($expectedChange): bool {
                    $this->assertSame($expectedChange->getJob(), $actualChange->getJob());
                    $this->assertSame($expectedChange->getInitiator(), $actualChange->getInitiator());
                    $this->assertSame($expectedChange->getStatus(), $actualChange->getStatus());
                    return true;
                }));

        $job->expects($this->once())
            ->method('setStatus')
            ->with($this->identicalTo($status))
            ->willReturnSelf();
        $job->expects($this->once())
            ->method('getChanges')
            ->willReturn($changes);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(2))
                      ->method('persist')
                      ->withConsecutive(
                          [$this->identicalTo($job)],
                          [$this->isInstanceOf(JobChange::class)],
                      );
        $entityManager->expects($this->once())
                      ->method('flush');

        $instance = new JobRepository($entityManager);
        $instance->addChange($job, $initiator, $status);
    }
}
