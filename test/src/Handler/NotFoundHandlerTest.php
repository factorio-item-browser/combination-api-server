<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Handler;

use FactorioItemBrowser\CombinationApi\Server\Exception\ApiEndpointNotFoundException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Handler\NotFoundHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The PHPUnit test of the NotFoundHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Handler\NotFoundHandler
 */
class NotFoundHandlerTest extends TestCase
{
    /**
     * @throws ServerException
     * @covers ::handle
     */
    public function testHandle(): void
    {
        $requestTarget = 'abc';

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getRequestTarget')
                ->willReturn($requestTarget);

        $this->expectException(ApiEndpointNotFoundException::class);

        $instance = new NotFoundHandler();
        $instance->handle($request);
    }
}
