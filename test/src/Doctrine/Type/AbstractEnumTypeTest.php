<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use FactorioItemBrowser\CombinationApi\Server\Doctrine\Type\AbstractEnumType;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the AbstractEnumType class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Doctrine\Type\AbstractEnumType
 */
class AbstractEnumTypeTest extends TestCase
{
    public function testGetSQLDeclaration(): void
    {
        $expectedResult = 'ENUM("foo","bar")';
        $fieldDeclaration = [];

        $platform = $this->createMock(AbstractPlatform::class);
        $platform->expects($this->any())
                 ->method('quoteStringLiteral')
                 ->with($this->isType('string'))
                 ->willReturnCallback(function (string $value): string {
                     return sprintf('"%s"', $value);
                 });

        $instance = $this->getMockBuilder(AbstractEnumType::class)
                         ->disableOriginalConstructor()
                         ->getMockForAbstractClass();

        $result = $instance->getSQLDeclaration($fieldDeclaration, $platform);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetName(): void
    {
        $expectedResult = 'enum';

        $instance = $this->getMockBuilder(AbstractEnumType::class)
                         ->disableOriginalConstructor()
                         ->getMockForAbstractClass();

        $result = $instance->getName();

        $this->assertSame($expectedResult, $result);
    }

    public function testRequiresSQLCommentHint(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);

        $instance = $this->getMockBuilder(AbstractEnumType::class)
                         ->disableOriginalConstructor()
                         ->getMockForAbstractClass();

        $result = $instance->requiresSQLCommentHint($platform);

        $this->assertTrue($result);
    }
}
