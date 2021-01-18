<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Helper;

use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use FactorioItemBrowser\CombinationApi\Client\Constant\ListOrder;
use FactorioItemBrowser\CombinationApi\Client\Transfer\Job as ClientJob;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job as DatabaseJob;
use FactorioItemBrowser\CombinationApi\Server\Helper\QueuePositionHelper;
use FactorioItemBrowser\CombinationApi\Server\Repository\JobRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the QueuePositionHelper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Helper\QueuePositionHelper
 */
class QueuePositionHelperTest extends TestCase
{
    public function testInjectQueuePosition(): void
    {
        $clientJob1 = new ClientJob();
        $clientJob1->id = '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76';
        $clientJob1->status = JobStatus::QUEUED;
        $clientJob2 = new ClientJob();
        $clientJob2->id = '01234567-89ab-cdef-0123-456789abcdef';
        $clientJob2->status = JobStatus::QUEUED;
        $clientJob3 = new ClientJob();
        $clientJob3->id = 'fedcba98-7654-3210-fedc-ba9876543210';
        $clientJob3->status = JobStatus::QUEUED;
        $clientJob4 = new ClientJob();
        $clientJob4->id = '00000000-0000-0000-0000-000000000000';
        $clientJob4->status = JobStatus::DONE;

        $databaseJob1 = new DatabaseJob();
        $databaseJob1->setId(Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'));
        $databaseJob2 = new DatabaseJob();
        $databaseJob2->setId(Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76'));

        $jobRepository = $this->createMock(JobRepository::class);
        $jobRepository->expects($this->once())
                      ->method('findAll')
                      ->with(
                          $this->isNull(),
                          $this->identicalTo(JobStatus::QUEUED),
                          $this->identicalTo(ListOrder::PRIORITY),
                          $this->identicalTo(10)
                      )
                      ->willReturn([$databaseJob1, $databaseJob2]);

        $instance = new QueuePositionHelper($jobRepository);

        $instance->injectQueuePosition($clientJob1);
        $this->assertSame($clientJob1->queuePosition, 2);

        $instance->injectQueuePosition($clientJob2);
        $this->assertSame($clientJob2->queuePosition, 10);

        $instance->injectQueuePosition($clientJob3);
        $this->assertSame($clientJob3->queuePosition, 1);

        $instance->injectQueuePosition($clientJob4);
        $this->assertSame($clientJob4->queuePosition, 0);
    }
}
