<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Service;

use FactorioItemBrowser\CombinationApi\Client\Constant\HeaderName;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Entity\Mod;
use FactorioItemBrowser\CombinationApi\Server\Exception\MissingCombinationHeaderException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Exception\UnknownCombinationException;
use FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator;
use FactorioItemBrowser\CombinationApi\Server\Repository\CombinationRepository;
use FactorioItemBrowser\CombinationApi\Server\Service\CombinationService;
use FactorioItemBrowser\CombinationApi\Server\Service\ModService;
use Laminas\Diactoros\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the CombinationService class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Service\CombinationService
 */
class CombinationServiceTest extends TestCase
{
    /** @var CombinationIdCalculator&MockObject */
    private CombinationIdCalculator $combinationIdCalculator;
    /** @var CombinationRepository&MockObject */
    private CombinationRepository $combinationRepository;
    /** @var ModService&MockObject */
    private ModService $modService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->combinationIdCalculator = $this->createMock(CombinationIdCalculator::class);
        $this->combinationRepository = $this->createMock(CombinationRepository::class);
        $this->modService = $this->createMock(ModService::class);
    }

    private function createInstance(): CombinationService
    {
        return new CombinationService(
            $this->combinationIdCalculator,
            $this->combinationRepository,
            $this->modService,
        );
    }

    /**
     * @throws ServerException
     */
    public function testGetCombinationById(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);

        $this->combinationRepository->expects($this->once())
                                    ->method('findById')
                                    ->with($this->identicalTo($combinationId))
                                    ->willReturn($combination);

        $instance = $this->createInstance();
        $result = $instance->getCombinationById($combinationId);

        $this->assertSame($combination, $result);
    }

    /**
     * @throws ServerException
     */
    public function testGetCombinationByIdWithException(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);

        $this->combinationRepository->expects($this->once())
                                    ->method('findById')
                                    ->with($this->identicalTo($combinationId))
                                    ->willReturn(null);

        $this->expectException(UnknownCombinationException::class);

        $instance = $this->createInstance();
        $instance->getCombinationById($combinationId);
    }

    public function testGetCombinationByModNamesWithExistingCombination(): void
    {
        $modNames = ['abc', 'def'];
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);

        $this->combinationIdCalculator->expects($this->once())
                                      ->method('fromModNames')
                                      ->with($this->identicalTo($modNames))
                                      ->willReturn($combinationId);

        $this->combinationRepository->expects($this->once())
                                    ->method('findById')
                                    ->with($this->identicalTo($combinationId))
                                    ->willReturn($combination);
        $this->combinationRepository->expects($this->never())
                                    ->method('create');

        $this->modService->expects($this->never())
                         ->method('getMods');

        $instance = $this->createInstance();
        $result = $instance->getCombinationByModNames($modNames);

        $this->assertSame($combination, $result);
    }

    public function testGetCombinationByModNamesWithNewCombination(): void
    {
        $modNames = ['abc', 'def'];
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);
        $mods = [
            $this->createMock(Mod::class),
            $this->createMock(Mod::class),
        ];

        $this->combinationIdCalculator->expects($this->once())
                                      ->method('fromModNames')
                                      ->with($this->identicalTo($modNames))
                                      ->willReturn($combinationId);

        $this->combinationRepository->expects($this->once())
                                    ->method('findById')
                                    ->with($this->identicalTo($combinationId))
                                    ->willReturn(null);
        $this->combinationRepository->expects($this->once())
                                    ->method('create')
                                    ->with($this->identicalTo($combinationId), $this->identicalTo($mods))
                                    ->willReturn($combination);

        $this->modService->expects($this->once())
                         ->method('getMods')
                         ->with($this->identicalTo($modNames))
                         ->willReturn($mods);

        $instance = $this->createInstance();
        $result = $instance->getCombinationByModNames($modNames);

        $this->assertSame($combination, $result);
    }

    /**
     * @throws ServerException
     */
    public function testGetCombinationFromRequestValue(): void
    {
        $requestValue = 'abc';
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);

        $this->combinationIdCalculator->expects($this->once())
                                      ->method('fromId')
                                      ->with($this->identicalTo($requestValue))
                                      ->willReturn($combinationId);

        $this->combinationRepository->expects($this->once())
                                    ->method('findById')
                                    ->with($this->identicalTo($combinationId))
                                    ->willReturn($combination);

        $instance = $this->createInstance();
        $result = $instance->getCombinationFromRequestValue($requestValue);

        $this->assertSame($combination, $result);
    }

    /**
     * @throws ServerException
     */
    public function testGetCombinationFromRequestHeaderWithCombinationId(): void
    {
        $headerCombinationId = 'abc';
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);

        $request = new Request();
        $request = $request->withHeader(HeaderName::COMBINATION_ID, $headerCombinationId);

        $this->combinationIdCalculator->expects($this->once())
                                      ->method('fromId')
                                      ->with($this->identicalTo($headerCombinationId))
                                      ->willReturn($combinationId);
        $this->combinationRepository->expects($this->once())
                                    ->method('findById')
                                    ->with($this->identicalTo($combinationId))
                                    ->willReturn($combination);

        $instance = $this->createInstance();
        $result = $instance->getCombinationFromRequestHeader($request);

        $this->assertSame($combination, $result);
    }

    /**
     * @throws ServerException
     */
    public function testGetCombinationFromRequestHeaderWithShortCombinationId(): void
    {
        $headerShortCombinationId = 'abc';
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);

        $request = new Request();
        $request = $request->withHeader(HeaderName::SHORT_COMBINATION_ID, $headerShortCombinationId);

        $this->combinationIdCalculator->expects($this->once())
                                      ->method('fromShortId')
                                      ->with($this->identicalTo($headerShortCombinationId))
                                      ->willReturn($combinationId);
        $this->combinationRepository->expects($this->once())
                                    ->method('findById')
                                    ->with($this->identicalTo($combinationId))
                                    ->willReturn($combination);

        $instance = $this->createInstance();
        $result = $instance->getCombinationFromRequestHeader($request);

        $this->assertSame($combination, $result);
    }

    /**
     * @throws ServerException
     */
    public function testGetCombinationFromRequestHeader(): void
    {
        $headerModNames = 'abc,def';
        $modNames = ['abc', 'def'];
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);

        $request = new Request();
        $request = $request->withHeader(HeaderName::MOD_NAMES, $headerModNames);

        $this->combinationIdCalculator->expects($this->once())
                                      ->method('fromModNames')
                                      ->with($this->identicalTo($modNames))
                                      ->willReturn($combinationId);
        $this->combinationRepository->expects($this->once())
                                    ->method('findById')
                                    ->with($this->identicalTo($combinationId))
                                    ->willReturn($combination);

        $instance = $this->createInstance();
        $result = $instance->getCombinationFromRequestHeader($request);

        $this->assertSame($combination, $result);
    }

    /**
     * @throws ServerException
     */
    public function testGetCombinationFromRequestHeaderWithMissingHeader(): void
    {
        $request = new Request();

        $this->expectException(MissingCombinationHeaderException::class);

        $instance = $this->createInstance();
        $instance->getCombinationFromRequestHeader($request);
    }
}
