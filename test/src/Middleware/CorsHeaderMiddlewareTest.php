<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Middleware;

use FactorioItemBrowser\CombinationApi\Server\Middleware\CorsHeaderMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The PHPUnit test of the CorsHeaderMiddleware class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Middleware\CorsHeaderMiddleware
 */
class CorsHeaderMiddlewareTest extends TestCase
{
    /** @var array<string> */
    private array $allowedOrigins = ['#foo.*#'];

    private function createInstance(): CorsHeaderMiddleware
    {
        return new CorsHeaderMiddleware(
            $this->allowedOrigins,
        );
    }

    public function testWithAllowedOrigin(): void
    {
        $serverParams = [
            'HTTP_ORIGIN' => 'foobar',
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->any())
                 ->method('hasHeader')
                 ->with($this->identicalTo('Allow'))
                 ->willReturn(true);
        $response->expects($this->any())
                 ->method('getHeaderLine')
                 ->with($this->identicalTo('Allow'))
                 ->willReturn('abc');
        $response->expects($this->exactly(4))
                 ->method('withHeader')
                 ->withConsecutive(
                     [$this->identicalTo('Access-Control-Max-Age'), $this->identicalTo('3600')],
                     [$this->identicalTo('Access-Control-Allow-Headers'), $this->isType('string')],
                     [$this->identicalTo('Access-Control-Allow-Origin'), $this->identicalTo('foobar')],
                     [$this->identicalTo('Access-Control-Allow-Methods'), $this->identicalTo('abc')],
                 )
                 ->willReturnSelf();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->any())
                ->method('getServerParams')
                ->willReturn($serverParams);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($request))
                ->willReturn($response);

        $instance = $this->createInstance();
        $result = $instance->process($request, $handler);

        $this->assertSame($response, $result);
    }

    public function testWithoutAllowedOrigin(): void
    {
        $serverParams = [
            'HTTP_ORIGIN' => 'not-allowed',
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->exactly(1))
                 ->method('withHeader')
                 ->withConsecutive(
                     [$this->identicalTo('Access-Control-Max-Age'), $this->identicalTo('3600')],
                 )
                 ->willReturnSelf();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->any())
                ->method('getServerParams')
                ->willReturn($serverParams);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($request))
                ->willReturn($response);

        $instance = $this->createInstance();
        $result = $instance->process($request, $handler);

        $this->assertSame($response, $result);
    }
}
