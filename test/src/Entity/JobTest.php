<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Entity\JobChange;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Job class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Entity\Job
 */
class JobTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getChanges
     */
    public function testConstruct(): void
    {
        $instance = new Job();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getChanges());
    }

    /**
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        $id = $this->createMock(UuidInterface::class);
        $instance = new Job();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    /**
     * @covers ::getCombination
     * @covers ::setCombination
     */
    public function testSetAndGetCombination(): void
    {
        $combination = $this->createMock(Combination::class);
        $instance = new Job();

        $this->assertSame($instance, $instance->setCombination($combination));
        $this->assertSame($combination, $instance->getCombination());
    }

    /**
     * @covers ::getPriority
     * @covers ::setPriority
     */
    public function testSetAndGetPriority(): void
    {
        $priority = 'abc';
        $instance = new Job();

        $this->assertSame($instance, $instance->setPriority($priority));
        $this->assertSame($priority, $instance->getPriority());
    }

    /**
     * @covers ::getStatus
     * @covers ::setStatus
     */
    public function testSetAndGetStatus(): void
    {
        $status = 'abc';
        $instance = new Job();

        $this->assertSame($instance, $instance->setStatus($status));
        $this->assertSame($status, $instance->getStatus());
    }

    /**
     * @covers ::getErrorMessage
     * @covers ::setErrorMessage
     */
    public function testSetAndGetErrorMessage(): void
    {
        $errorMessage = 'abc';
        $instance = new Job();

        $this->assertSame($instance, $instance->setErrorMessage($errorMessage));
        $this->assertSame($errorMessage, $instance->getErrorMessage());
    }

    /**
     * @covers ::getCreationTime
     */
    public function testGetCreationTime(): void
    {
        $change1 = new JobChange();
        $change1->setStatus(JobStatus::DONE)
                ->setTimestamp(new DateTimeImmutable('2038-01-19 03:14:07+00:00'));

        $change2 = new JobChange();
        $change2->setStatus(JobStatus::QUEUED)
                ->setTimestamp(new DateTimeImmutable('2038-01-19 02:14:07+00:00'));

        $expectedResult = new DateTimeImmutable('2038-01-19 02:14:07+00:00');

        $instance = new Job();
        $instance->getChanges()->add($change1);
        $instance->getChanges()->add($change2);

        $result = $instance->getCreationTime();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers ::getCreationTime
     */
    public function testGetCreationTimeWithoutQueuedChange(): void
    {
        $instance = new Job();

        $result = $instance->getCreationTime();
        $this->assertInstanceOf(DateTimeImmutable::class, $result);
    }
}
