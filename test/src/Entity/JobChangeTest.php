<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Entity;

use DateTimeImmutable;
use FactorioItemBrowser\CombinationApi\Server\Entity\Job;
use FactorioItemBrowser\CombinationApi\Server\Entity\JobChange;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the JobChange class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Entity\JobChange
 */
class JobChangeTest extends TestCase
{
    public function testSetAndGetId(): void
    {
        $id = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $instance = new JobChange();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    public function testSetAndGetJob(): void
    {
        $job = $this->createMock(Job::class);
        $instance = new JobChange();

        $this->assertSame($instance, $instance->setJob($job));
        $this->assertSame($job, $instance->getJob());
    }

    public function testSetAndGetInitiator(): void
    {
        $initiator = 'abc';
        $instance = new JobChange();

        $this->assertSame($instance, $instance->setInitiator($initiator));
        $this->assertSame($initiator, $instance->getInitiator());
    }

    public function testSetAndGetStatus(): void
    {
        $status = 'abc';
        $instance = new JobChange();

        $this->assertSame($instance, $instance->setStatus($status));
        $this->assertSame($status, $instance->getStatus());
    }

    public function testSetAndGetTimestamp(): void
    {
        $timestamp = $this->createMock(DateTimeImmutable::class);
        $instance = new JobChange();

        $this->assertSame($instance, $instance->setTimestamp($timestamp));
        $this->assertSame($timestamp, $instance->getTimestamp());
    }
}
