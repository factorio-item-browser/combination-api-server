<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Helper;

use FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the CombinationIdCalculator class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator
 */
class CombinationIdCalculatorTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function provideFromModNames(): array
    {
        return [
            [['base'], '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76'],
            [['base', 'Foo', 'Bar'], '83f899a6-ba39-8f56-a704-92907650dc3e'],
        ];
    }

    /**
     * @param array<string> $modNames
     * @param string $expectedId
     * @covers ::__construct
     * @covers ::fromModNames
     * @dataProvider provideFromModNames
     */
    public function testFromModNames(array $modNames, string $expectedId): void
    {
        $instance = new CombinationIdCalculator();
        $result = $instance->fromModNames($modNames);
        $this->assertSame($expectedId, $result->toString());
    }

    /**
     * @covers ::__construct
     * @covers ::fromId
     */
    public function testFromId(): void
    {
        $combinationId = '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76';
        $instance = new CombinationIdCalculator();
        $result = $instance->fromId($combinationId);

        $this->assertSame($combinationId, $result->toString());
    }

    /**
     * @return array<mixed>
     */
    public function provideFromShortId(): array
    {
        return [
            ['1reA6H5z4uFpotvegbLIr4', '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76'],
            ['411BxkkQAqZsly1XYs0u5g', '83f899a6-ba39-8f56-a704-92907650dc3e'],
            ['0000000000000000000000', '00000000-0000-0000-0000-000000000000'],
            ['0000000000000000000001', '00000000-0000-0000-0000-000000000001'],
            ['7N42dgm5tFLK9N8MT7fHC7', 'ffffffff-ffff-ffff-ffff-ffffffffffff'],
            ['ZZZZZZZZZZZZZZZZZZZZZZ', 'f520034c-4307-70c4-2452-8c66503fffff'], // out of range, overflows uuid
        ];
    }

    /**
     * @param string $shortId
     * @param string $expectedId
     * @covers ::__construct
     * @covers ::fromShortId
     * @dataProvider provideFromShortId
     */
    public function testFromShortId(string $shortId, string $expectedId): void
    {
        $instance = new CombinationIdCalculator();
        $result = $instance->fromShortId($shortId);

        $this->assertSame($expectedId, $result->toString());
    }

    /**
     * @return array<mixed>
     */
    public function provideToShortId(): array
    {
        return [
            ['2f4a45fa-a509-a9d1-aae6-ffcf984a7a76', '1reA6H5z4uFpotvegbLIr4'],
            ['83f899a6-ba39-8f56-a704-92907650dc3e', '411BxkkQAqZsly1XYs0u5g'],
            ['00000000-0000-0000-0000-000000000000', '0000000000000000000000'],
            ['00000000-0000-0000-0000-000000000001', '0000000000000000000001'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', '7N42dgm5tFLK9N8MT7fHC7'],
        ];
    }

    /**
     * @param string $combinationId
     * @param string $expectedResult
     * @covers ::__construct
     * @covers ::toShortId
     * @dataProvider provideToShortId
     */
    public function testToShortId(string $combinationId, string $expectedResult): void
    {
        $instance = new CombinationIdCalculator();
        $result = $instance->toShortId(Uuid::fromString($combinationId));

        $this->assertSame($expectedResult, $result);
    }
}
