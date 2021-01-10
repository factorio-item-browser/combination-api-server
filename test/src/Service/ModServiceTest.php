<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Service;

use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use FactorioItemBrowser\CombinationApi\Server\Repository\ModRepository;
use FactorioItemBrowser\CombinationApi\Server\Service\ModService;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ModService class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Service\ModService
 */
class ModServiceTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getMods
     */
    public function testGetMods(): void
    {
        $modNames = ['abc', 'def', 'ghi'];

        $mod1 = new Mod();
        $mod1->setName('abc');
        $mod2 = new Mod();
        $mod2->setName('def');
        $mod3 = new Mod();
        $mod3->setName('ghi');

        $expectedResult = [
            'abc' => $mod1,
            'def' => $mod2,
            'ghi' => $mod3,
        ];

        $modRepository = $this->createMock(ModRepository::class);
        $modRepository->expects($this->once())
                      ->method('findByNames')
                      ->with($this->identicalTo($modNames))
                      ->willReturn([$mod1, $mod3]);
        $modRepository->expects($this->once())
                      ->method('create')
                      ->with($this->identicalTo('def'))
                      ->willReturn($mod2);

        $instance = new ModService($modRepository);
        $result = $instance->getMods($modNames);

        $this->assertEquals($expectedResult, $result);
    }
}
