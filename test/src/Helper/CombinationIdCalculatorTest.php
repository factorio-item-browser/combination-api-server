<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Helper;

use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidCombinationIdException;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidShortCombinationIdException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
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
     * @return array<mixed>
     */
    public function provideFromId(): array
    {
        return [
            ['2f4a45fa-a509-a9d1-aae6-ffcf984a7a76', false],
            ['83f899a6-ba39-8f56-a704-92907650dc3e', false],
            ['00000000-0000-0000-0000-000000000000', false],
            ['00000000-0000-0000-0000-000000000001', false],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', false],

            ['1234', true],
            ['2f4a45fa-a509-a9d1-aae6-ffcf984a7a76-bad', true],
        ];
    }

    /**
     * @param string $combinationId
     * @param bool $expectException
     * @throws ServerException
     * @covers ::__construct
     * @covers ::fromId
     * @dataProvider provideFromId
     */
    public function testFromId(string $combinationId, bool $expectException): void
    {
        if ($expectException) {
            $this->expectException(InvalidCombinationIdException::class);
        }

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
            ['1reA6H5z4uFpotvegbLIr4', '2f4a45fa-a509-a9d1-aae6-ffcf984a7a76', false],
            ['411BxkkQAqZsly1XYs0u5g', '83f899a6-ba39-8f56-a704-92907650dc3e', false],
            ['0000000000000000000000', '00000000-0000-0000-0000-000000000000', false],
            ['0000000000000000000001', '00000000-0000-0000-0000-000000000001', false],
            ['7N42dgm5tFLK9N8MT7fHC7', 'ffffffff-ffff-ffff-ffff-ffffffffffff', false],

            ['ZZZZZZZZZZZZZZZZZZZZZZ', 'f520034c-4307-70c4-2452-8c66503fffff', false], // out of range, overflows uuid
            ['1reA6H5z4uFpotvegbLIr-', '', true],
        ];
    }

    /**
     * @param string $shortId
     * @param string $expectedId
     * @param bool $expectException
     * @throws ServerException
     * @covers ::__construct
     * @covers ::fromShortId
     * @dataProvider provideFromShortId
     */
    public function testFromShortId(string $shortId, string $expectedId, bool $expectException): void
    {
        if ($expectException) {
            $this->expectException(InvalidShortCombinationIdException::class);
        }

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
