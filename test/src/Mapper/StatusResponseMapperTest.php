<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Mapper;

use DateTimeImmutable;
use FactorioItemBrowser\CombinationApi\Client\Response\Combination\StatusResponse;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator;
use FactorioItemBrowser\CombinationApi\Server\Mapper\StatusResponseMapper;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the StatusResponseMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Mapper\StatusResponseMapper
 */
class StatusResponseMapperTest extends TestCase
{
    public function testMeta(): void
    {
        $instance = new StatusResponseMapper($this->createMock(CombinationIdCalculator::class));

        $this->assertSame(Combination::class, $instance->getSupportedSourceClass());
        $this->assertSame(StatusResponse::class, $instance->getSupportedDestinationClass());
    }

    public function testMap(): void
    {
        $combinationId = '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76';
        $mod1 = new Mod();
        $mod1->setName('abc');
        $mod2 = new Mod();
        $mod2->setName('def');
        $destination = new StatusResponse();

        $source = new Combination();
        $source->setId(Uuid::fromString($combinationId));
        $source->setExportTime(new DateTimeImmutable('2038-01-19 03:14:07+00:00'));
        $source->getMods()->add($mod1);
        $source->getMods()->add($mod2);

        $expectedDestination = new StatusResponse();
        $expectedDestination->id = '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76';
        $expectedDestination->shortId = '1reA6H5z4uFpotvegbLIr4';
        $expectedDestination->modNames = ['abc', 'def'];
        $expectedDestination->isDataAvailable = true;
        $expectedDestination->exportTime = new DateTimeImmutable('2038-01-19 03:14:07+00:00');

        $instance = new StatusResponseMapper(new CombinationIdCalculator());
        $instance->map($source, $destination);

        $this->assertEquals($expectedDestination, $destination);
    }
}
