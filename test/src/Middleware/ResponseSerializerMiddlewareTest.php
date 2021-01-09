<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Middleware;

use FactorioItemBrowser\CombinationApi\Server\Middleware\ResponseSerializerMiddleware;
use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The PHPUnit test of the ResponseSerializerMiddleware class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ResponseSerializerMiddlewareTest extends TestCase
{
    public function testProcess(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $response2 = $this->createMock(ClientResponse::class);

        $response1 = $this->createMock(ClientResponse::class);
        $response1->expects($this->once())
                  ->method('withSerializer')
                  ->with($this->identicalTo($serializer))
                  ->willReturn($response2);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($request))
                ->willReturn($response1);

        $instance = new ResponseSerializerMiddleware($serializer);
        $result = $instance->process($request, $handler);

        $this->assertSame($response2, $result);
    }

    public function testProcessWithoutClientResponse(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($request))
                ->willReturn($response);

        $instance = new ResponseSerializerMiddleware($serializer);
        $result = $instance->process($request, $handler);

        $this->assertSame($response, $result);
    }
}
