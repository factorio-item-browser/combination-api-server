<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Mapper;

use FactorioItemBrowser\CombinationApi\Client\Response\StatusResponse;
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
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Mapper\StatusResponseMapper
 */
class StatusResponseMapperTest extends TestCase
{
    /**
     * @covers ::getSupportedDestinationClass
     * @covers ::getSupportedSourceClass
     */
    public function testMeta(): void
    {
        $instance = new StatusResponseMapper($this->createMock(CombinationIdCalculator::class));

        $this->assertSame(Combination::class, $instance->getSupportedSourceClass());
        $this->assertSame(StatusResponse::class, $instance->getSupportedDestinationClass());
    }

    /**
     * @covers ::__construct
     * @covers ::map
     */
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
        $source->getMods()->add($mod1);
        $source->getMods()->add($mod2);

        $expectedDestination = new StatusResponse();
        $expectedDestination->id = '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76';
        $expectedDestination->shortId = '1reA6H5z4uFpotvegbLIr4';
        $expectedDestination->modNames = ['abc', 'def'];
        $expectedDestination->isDataAvailable = false;

        $instance = new StatusResponseMapper(new CombinationIdCalculator());
        $instance->map($source, $destination);

        $this->assertEquals($expectedDestination, $destination);
    }
}
