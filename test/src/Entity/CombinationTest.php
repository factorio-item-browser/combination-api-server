<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Combination class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Entity\Combination
 */
class CombinationTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getJobs
     * @covers ::getMods
     */
    public function testConstruct(): void
    {
        $instance = new Combination();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getJobs());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getMods());
    }

    /**
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        $id = $this->createMock(UuidInterface::class);
        $instance = new Combination();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    /**
     * @covers ::getModNames
     */
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
}
