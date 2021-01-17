<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\CombinationApi\Client\Constant\JobStatus;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Combination class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Entity\Combination
 */
class CombinationTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new Combination();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getJobs());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getMods());
    }

    public function testSetAndGetId(): void
    {
        $id = $this->createMock(UuidInterface::class);
        $instance = new Combination();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    public function testSetAndGetExportTime(): void
    {
        $exportTime = new DateTimeImmutable('2038-01-19 03:14:07+00:00');
        $instance = new Combination();

        $this->assertSame($instance, $instance->setExportTime($exportTime));
        $this->assertSame($exportTime, $instance->getExportTime());
    }

    public function testGetModNames(): void
    {
        $mod1 = new Mod();
        $mod1->setName('abc');
        $mod2 = new Mod();
        $mod2->setName('def');
        $expectedResult = ['abc', 'def'];

        $instance = new Combination();
        $instance->getMods()->add($mod1);
        $instance->getMods()->add($mod2);

        $result = $instance->getModNames();
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetUnfinishedJob(): void
    {
        $job1 = new Job();
        $job1->setStatus(JobStatus::DONE);
        $job2 = new Job();
        $job2->setStatus(JobStatus::ERROR);
        $job3 = new Job();
        $job3->setStatus(JobStatus::QUEUED);
        $job4 = new Job();
        $job4->setStatus(JobStatus::DONE);

        $instance = new Combination();
        $instance->getJobs()->add($job1);
        $instance->getJobs()->add($job2);
        $instance->getJobs()->add($job3);
        $instance->getJobs()->add($job4);

        $result = $instance->getUnfinishedJob();
        $this->assertSame($job3, $result);
    }

    public function testGetUnfinishedJobWithoutJob(): void
    {
        $job1 = new Job();
        $job1->setStatus(JobStatus::DONE);
        $job2 = new Job();
        $job2->setStatus(JobStatus::ERROR);
        $job3 = new Job();
        $job3->setStatus(JobStatus::DONE);

        $instance = new Combination();
        $instance->getJobs()->add($job1);
        $instance->getJobs()->add($job2);
        $instance->getJobs()->add($job3);

        $result = $instance->getUnfinishedJob();
        $this->assertNull($result);
    }
}
