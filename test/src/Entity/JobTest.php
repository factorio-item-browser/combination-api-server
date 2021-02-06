<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Job class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Entity\Job
 */
class JobTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new Job();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getChanges());
    }

    public function testSetAndGetId(): void
    {
        $id = $this->createMock(UuidInterface::class);
        $instance = new Job();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    public function testSetAndGetCombination(): void
    {
        $combination = $this->createMock(Combination::class);
        $instance = new Job();

        $this->assertSame($instance, $instance->setCombination($combination));
        $this->assertSame($combination, $instance->getCombination());
    }

    public function testSetAndGetPriority(): void
    {
        $priority = 'abc';
        $instance = new Job();

        $this->assertSame($instance, $instance->setPriority($priority));
        $this->assertSame($priority, $instance->getPriority());
    }

    public function testSetAndGetStatus(): void
    {
        $status = 'abc';
        $instance = new Job();

        $this->assertSame($instance, $instance->setStatus($status));
        $this->assertSame($status, $instance->getStatus());
    }

    public function testSetAndGetCreationTime(): void
    {
        $value = new DateTimeImmutable('2038-01-19 03:14:00+00:00');
        $instance = new Job();

        $this->assertSame($instance, $instance->setCreationTime($value));
        $this->assertSame($value, $instance->getCreationTime());
    }

    public function testSetAndGetErrorMessage(): void
    {
        $errorMessage = 'abc';
        $instance = new Job();

        $this->assertSame($instance, $instance->setErrorMessage($errorMessage));
        $this->assertSame($errorMessage, $instance->getErrorMessage());
    }

    public function testGetCreationTimeWithoutQueuedChange(): void
    {
        $instance = new Job();

        $result = $instance->getCreationTime();
        $this->assertInstanceOf(DateTimeImmutable::class, $result);
    }
}
