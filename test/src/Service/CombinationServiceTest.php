<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Service;

use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator;
use FactorioItemBrowser\CombinationApi\Server\Repository\CombinationRepository;
use FactorioItemBrowser\CombinationApi\Server\Service\CombinationService;
use FactorioItemBrowser\CombinationApi\Server\Service\ModService;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the CombinationService class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Service\CombinationService
 */
class CombinationServiceTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getCombinationByModNames
     */
    public function testGetCombinationByModNamesWithExistingCombination(): void
    {
        $modNames = ['abc', 'def'];
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);

        $combinationIdCalculator = $this->createMock(CombinationIdCalculator::class);
        $combinationIdCalculator->expects($this->once())
                                ->method('fromModNames')
                                ->with($this->identicalTo($modNames))
                                ->willReturn($combinationId);

        $combinationRepository = $this->createMock(CombinationRepository::class);
        $combinationRepository->expects($this->once())
                              ->method('findById')
                              ->with($this->identicalTo($combinationId))
                              ->willReturn($combination);
        $combinationRepository->expects($this->never())
                              ->method('create');

        $modService = $this->createMock(ModService::class);
        $modService->expects($this->never())
                   ->method('getMods');

        $instance = new CombinationService($combinationIdCalculator, $combinationRepository, $modService);
        $result = $instance->getCombinationByModNames($modNames);

        $this->assertSame($combination, $result);
    }

    /**
     * @covers ::__construct
     * @covers ::getCombinationByModNames
     */
    public function testGetCombinationByModNamesWithNewCombination(): void
    {
        $modNames = ['abc', 'def'];
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);
        $mods = [
            $this->createMock(Mod::class),
            $this->createMock(Mod::class),
        ];

        $combinationIdCalculator = $this->createMock(CombinationIdCalculator::class);
        $combinationIdCalculator->expects($this->once())
                                ->method('fromModNames')
                                ->with($this->identicalTo($modNames))
                                ->willReturn($combinationId);

        $combinationRepository = $this->createMock(CombinationRepository::class);
        $combinationRepository->expects($this->once())
                              ->method('findById')
                              ->with($this->identicalTo($combinationId))
                              ->willReturn(null);
        $combinationRepository->expects($this->once())
                              ->method('create')
                              ->with($this->identicalTo($combinationId), $this->identicalTo($mods))
                              ->willReturn($combination);

        $modService = $this->createMock(ModService::class);
        $modService->expects($this->once())
                   ->method('getMods')
                   ->with($this->identicalTo($modNames))
                   ->willReturn($mods);

        $instance = new CombinationService($combinationIdCalculator, $combinationRepository, $modService);
        $result = $instance->getCombinationByModNames($modNames);

        $this->assertSame($combination, $result);
    }
}
