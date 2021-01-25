<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Handler\Combination;

use BluePsyduck\FactorioModPortalClient\Entity\Version;
use FactorioItemBrowser\CombinationApi\Client\Response\Combination\ValidateResponse;
use FactorioItemBrowser\CombinationApi\Client\Transfer\ValidatedMod;
use FactorioItemBrowser\CombinationApi\Client\Transfer\ValidationProblem;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Handler\Combination\ValidateHandler;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\CombinationService;
use FactorioItemBrowser\CombinationApi\Server\Service\ValidationService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The PHPUnit test of the ValidateHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Handler\Combination\ValidateHandler
 */
class ValidateHandlerTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function provideHandle(): array
    {
        $validatedMod1 = new ValidatedMod();
        $validatedMod1->problems = [];
        $validatedMod2 = new ValidatedMod();
        $validatedMod2->problems = [];
        $validatedMod3 = new ValidatedMod();
        $validatedMod3->problems = [new ValidationProblem()];
        $validatedMod4 = new ValidatedMod();
        $validatedMod4->problems = [new ValidationProblem()];

        return [
            [[$validatedMod1, $validatedMod2], true],
            [[$validatedMod3, $validatedMod4], false],
            [[$validatedMod1, $validatedMod2, $validatedMod3], false],
        ];
    }

    /**
     * @param array<ValidatedMod> $validatedMods
     * @param bool $isValid
     * @throws ServerException
     * @dataProvider provideHandle
     */
    public function testHandle(array $validatedMods, bool $isValid): void
    {
        $modNames = ['abc', 'def'];
        $factorioVersion = new Version('1.2.3');

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo('factorio-version'))
                ->willReturn('1.2.3');

        $combination = $this->createMock(Combination::class);
        $combination->expects($this->once())
                    ->method('getModNames')
                    ->willReturn($modNames);

        $expectedPayload = new ValidateResponse();
        $expectedPayload->mods = $validatedMods;
        $expectedPayload->isValid = $isValid;

        $combinationService = $this->createMock(CombinationService::class);
        $combinationService->expects($this->once())
                           ->method('getCombinationFromRequestHeader')
                           ->with($this->identicalTo($request))
                           ->willReturn($combination);

        $validationService = $this->createMock(ValidationService::class);
        $validationService->expects($this->once())
                          ->method('validate')
                          ->with($this->identicalTo($modNames), $this->equalTo($factorioVersion))
                          ->willReturn($validatedMods);

        $instance = new ValidateHandler($combinationService, $validationService);
        $result = $instance->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
        /** @var ClientResponse $result */
        $this->assertEquals($expectedPayload, $result->getPayload());
    }
}
