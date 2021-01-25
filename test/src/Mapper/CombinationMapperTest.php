<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Mapper;

use DateTimeImmutable;
use FactorioItemBrowser\CombinationApi\Client\Response\Combination\StatusResponse;
use FactorioItemBrowser\CombinationApi\Client\Transfer\Combination as ClientCombination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination as DatabaseCombination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator;
use FactorioItemBrowser\CombinationApi\Server\Mapper\CombinationMapper;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

/**
 * The PHPUnit test of the CombinationMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Mapper\CombinationMapper
 */
class CombinationMapperTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function provideSupports(): array
    {
        return [
            [new DatabaseCombination(), new ClientCombination(), true],
            [new DatabaseCombination(), new StatusResponse(), true],

            [new DatabaseCombination(), new stdClass(), false],
            [new stdClass(), new ClientCombination(), false],
        ];
    }

    /**
     * @param object $source
     * @param object $destination
     * @param bool $expectedResult
     * @dataProvider provideSupports
     */
    public function testSupports(object $source, object $destination, bool $expectedResult): void
    {
        $instance = new CombinationMapper(new CombinationIdCalculator());
        $result = $instance->supports($source, $destination);

        $this->assertSame($expectedResult, $result);
    }

    public function testMap(): void
    {
        $combinationId = '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76';
        $mod1 = new Mod();
        $mod1->setName('abc');
        $mod2 = new Mod();
        $mod2->setName('def');
        $destination = new StatusResponse();

        $source = new DatabaseCombination();
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

        $instance = new CombinationMapper(new CombinationIdCalculator());
        $instance->map($source, $destination);

        $this->assertEquals($expectedDestination, $destination);
    }
}
