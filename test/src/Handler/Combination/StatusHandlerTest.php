<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Handler\Combination;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\CombinationApi\Client\Response\Combination\StatusResponse;
use FactorioItemBrowser\CombinationApi\Server\Entity\Combination;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Handler\Combination\StatusHandler;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use FactorioItemBrowser\CombinationApi\Server\Service\CombinationService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The PHPUnit test of the StatusHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Handler\Combination\StatusHandler
 */
class StatusHandlerTest extends TestCase
{
    /**
     * @throws ServerException
     */
    public function testHandle(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $combination = $this->createMock(Combination::class);
        $statusResponse = $this->createMock(StatusResponse::class);

        $combinationService = $this->createMock(CombinationService::class);
        $combinationService->expects($this->once())
                           ->method('getCombinationFromRequestHeader')
                           ->with($this->identicalTo($request))
                           ->willReturn($combination);

        $mapperManager = $this->createMock(MapperManagerInterface::class);
        $mapperManager->expects($this->once())
                      ->method('map')
                      ->with($this->identicalTo($combination), $this->isInstanceOf(StatusResponse::class))
                      ->willReturn($statusResponse);

        $instance = new StatusHandler($combinationService, $mapperManager);
        $result = $instance->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
        /** @var ClientResponse $result */
        $this->assertSame($statusResponse, $result->getPayload());
    }
}
