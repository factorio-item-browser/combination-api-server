<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Service;

use DateTimeImmutable;
use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use FactorioItemBrowser\CombinationApi\Server\Entity\Agent;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Exception\ActionNotAllowedException;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidJobIdException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Exception\UnknownJobException;
use FactorioItemBrowser\CombinationApi\Server\Repository\AgentRepository;
use FactorioItemBrowser\CombinationApi\Server\Repository\JobRepository;
use FactorioItemBrowser\CombinationApi\Server\Service\JobService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the JobService class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Service\JobService
 */
class JobServiceTest extends TestCase
{
    /** @var AgentRepository&MockObject */
    private AgentRepository $agentRepository;
    /** @var JobRepository&MockObject */
    private JobRepository $jobRepository;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->jobRepository = $this->createMock(JobRepository::class);
    }

    private function createInstance(): JobService
    {
        return new JobService(
            $this->agentRepository,
            $this->jobRepository,
        );
    }

    /**
     * @throws ServerException
     */
    public function testGetJobFromRequestValue(): void
    {
        $jobId = '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76';
        $id = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $job = $this->createMock(Job::class);

        $this->jobRepository->expects($this->once())
                            ->method('findById')
                            ->with($this->equalTo($id))
                            ->willReturn($job);

        $instance = $this->createInstance();
        $result = $instance->getJobFromRequestValue($jobId);

        $this->assertSame($job, $result);
    }

    /**
     * @throws ServerException
     */
    public function testGetJobFromRequestValueWithInvalidJobId(): void
    {
        $jobId = 'invalid';

        $this->jobRepository->expects($this->never())
                            ->method('findById');

        $this->expectException(InvalidJobIdException::class);

        $instance = $this->createInstance();
        $instance->getJobFromRequestValue($jobId);
    }

    /**
     * @throws ServerException
     */
    public function testGetJobFromRequestValueWithoutJob(): void
    {
        $jobId = '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76';
        $id = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $this->jobRepository->expects($this->once())
                            ->method('findById')
                            ->with($this->equalTo($id))
                            ->willReturn(null);

        $this->expectException(UnknownJobException::class);

        $instance = $this->createInstance();
        $instance->getJobFromRequestValue($jobId);
    }

    public function testGetJobsFromQueryParams(): void
    {
        $queryParams = [
            'combinationId' => '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76',
            'status' => 'abc',
            'order' => 'def',
            'limit' => '42',
            'first' => '21',
        ];
        $jobs = [
            $this->createMock(Job::class),
            $this->createMock(Job::class),
        ];

        $this->jobRepository->expects($this->once())
                            ->method('findAll')
                            ->with(
                                $this->equalTo(Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76')),
                                $this->identicalTo('abc'),
                                $this->identicalTo('def'),
                                $this->identicalTo(42),
                                $this->identicalTo(21),
                            )
                            ->willReturn($jobs);

        $instance = $this->createInstance();
        $result = $instance->getJobsFromQueryParams($queryParams);

        $this->assertSame($jobs, $result);
    }

    public function testGetJobsFromQueryParamsWithoutValues(): void
    {
        $jobs = [
            $this->createMock(Job::class),
            $this->createMock(Job::class),
        ];

        $this->jobRepository->expects($this->once())
                            ->method('findAll')
                            ->with(
                                $this->isNull(),
                                $this->identicalTo(''),
                                $this->identicalTo(''),
                                $this->identicalTo(10),
                                $this->identicalTo(0),
                            )
                            ->willReturn($jobs);

        $instance = $this->createInstance();
        $result = $instance->getJobsFromQueryParams([]);

        $this->assertSame($jobs, $result);
    }

    /**
     * @throws ServerException
     */
    public function testCreateJobFromCombination(): void
    {
        $combination = $this->createMock(Combination::class);
        $priority = 'abc';
        $job = $this->createMock(Job::class);

        $agent = new Agent();
        $agent->setName('def')
              ->setCanCreateJobs(true);

        $this->agentRepository->expects($this->once())
                              ->method('getCurrentAgent')
                              ->willReturn($agent);
        $this->jobRepository->expects($this->once())
                            ->method('create')
                            ->with(
                                $this->identicalTo($combination),
                                $this->identicalTo('def'),
                                $this->identicalTo($priority),
                            )
                            ->willReturn($job);

        $instance = $this->createInstance();
        $result = $instance->createJobForCombination($combination, $priority);

        $this->assertSame($job, $result);
    }

    /**
     * @throws ServerException
     */
    public function testCreateJobFromCombinationWithoutPermission(): void
    {
        $combination = $this->createMock(Combination::class);
        $priority = 'abc';

        $agent = new Agent();
        $agent->setName('def')
              ->setCanCreateJobs(false);

        $this->agentRepository->expects($this->once())
                              ->method('getCurrentAgent')
                              ->willReturn($agent);
        $this->jobRepository->expects($this->never())
                            ->method('create');

        $this->expectException(ActionNotAllowedException::class);

        $instance = $this->createInstance();
        $instance->createJobForCombination($combination, $priority);
    }

    /**
     * @throws ServerException
     */
    public function testChangeJob(): void
    {
        $status = 'abc';
        $errorMessage = 'def';

        $agent = new Agent();
        $agent->setName('ghi')
              ->setCanUpdateJobs(true);

        $combination = $this->createMock(Combination::class);
        $combination->expects($this->never())
                    ->method('setExportTime');

        $job = $this->createMock(Job::class);
        $job->expects($this->any())
            ->method('getCombination')
            ->willReturn($combination);
        $job->expects($this->once())
            ->method('setErrorMessage')
            ->with($this->identicalTo($errorMessage))
            ->willReturnSelf();

        $this->agentRepository->expects($this->once())
                              ->method('getCurrentAgent')
                              ->willReturn($agent);
        $this->jobRepository->expects($this->once())
                            ->method('addChange')
                            ->with($this->identicalTo($job), $this->identicalTo('ghi'), $this->identicalTo($status));

        $instance = $this->createInstance();
        $instance->changeJob($job, $status, $errorMessage);
    }

    /**
     * @throws ServerException
     */
    public function testChangeJobWithDoneStatus(): void
    {
        $status = JobStatus::DONE;
        $errorMessage = 'def';

        $agent = new Agent();
        $agent->setName('ghi')
              ->setCanUpdateJobs(true);

        $combination = $this->createMock(Combination::class);
        $combination->expects($this->once())
                    ->method('setExportTime')
                    ->with($this->isInstanceOf(DateTimeImmutable::class))
                    ->willReturnSelf();

        $job = $this->createMock(Job::class);
        $job->expects($this->any())
            ->method('getCombination')
            ->willReturn($combination);
        $job->expects($this->once())
            ->method('setErrorMessage')
            ->with($this->identicalTo($errorMessage))
            ->willReturnSelf();

        $this->agentRepository->expects($this->once())
                              ->method('getCurrentAgent')
                              ->willReturn($agent);
        $this->jobRepository->expects($this->once())
                            ->method('addChange')
                            ->with($this->identicalTo($job), $this->identicalTo('ghi'), $this->identicalTo($status));

        $instance = $this->createInstance();
        $instance->changeJob($job, $status, $errorMessage);
    }

    /**
     * @throws ServerException
     */
    public function testChangeJobWithoutPermission(): void
    {
        $status = 'abc';
        $errorMessage = 'def';

        $agent = new Agent();
        $agent->setName('ghi')
              ->setCanUpdateJobs(false);

        $combination = $this->createMock(Combination::class);
        $combination->expects($this->never())
                    ->method('setExportTime');

        $job = $this->createMock(Job::class);
        $job->expects($this->any())
            ->method('getCombination')
            ->willReturn($combination);

        $this->agentRepository->expects($this->once())
                              ->method('getCurrentAgent')
                              ->willReturn($agent);
        $this->jobRepository->expects($this->never())
                            ->method('addChange');

        $this->expectException(ActionNotAllowedException::class);

        $instance = $this->createInstance();
        $instance->changeJob($job, $status, $errorMessage);
    }
}
