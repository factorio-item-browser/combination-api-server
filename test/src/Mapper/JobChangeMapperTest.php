<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Mapper;

use DateTimeImmutable;
use FactorioItemBrowser\CombinationApi\Client\Transfer\JobChange as ClientJobChange;
use FactorioItemBrowser\CombinationApi\Server\Entity\JobChange as DatabaseJobChange;
use FactorioItemBrowser\CombinationApi\Server\Mapper\JobChangeMapper;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the JobChangeMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Mapper\JobChangeMapper
 */
class JobChangeMapperTest extends TestCase
{
    public function testMeta(): void
    {
        $instance = new JobChangeMapper();

        $this->assertSame(DatabaseJobChange::class, $instance->getSupportedSourceClass());
        $this->assertSame(ClientJobChange::class, $instance->getSupportedDestinationClass());
    }

    public function testMap(): void
    {
        $timestamp = new DateTimeImmutable('2038-01-19 03:14:07');

        $source = new DatabaseJobChange();
        $source->setInitiator('abc')
               ->setStatus('def')
               ->setTimestamp($timestamp);

        $expectedDestination = new ClientJobChange();
        $expectedDestination->initiator = 'abc';
        $expectedDestination->status = 'def';
        $expectedDestination->timestamp = $timestamp;

        $destination = new ClientJobChange();

        $instance = new JobChangeMapper();
        $instance->map($source, $destination);

        $this->assertEquals($expectedDestination, $destination);
    }
}
