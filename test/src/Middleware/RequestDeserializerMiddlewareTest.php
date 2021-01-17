<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Middleware;

use Exception;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidRequestBodyException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Middleware\RequestDeserializerMiddleware;
use JMS\Serializer\SerializerInterface;
use Laminas\Diactoros\Stream;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

/**
 * The PHPUnit test of the RequestDeserializerMiddleware class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Middleware\RequestDeserializerMiddleware
 */
class RequestDeserializerMiddlewareTest extends TestCase
{
    /**
     * @throws ServerException
     */
    public function testProcess(): void
    {
        $matchedRouteName = 'abc';
        $requestClass = 'stdClass';
        $requestClassesByRoutes = [
            'abc' => 'stdClass',
        ];
        $requestBody = 'def';
        $parsedBody = $this->createMock(stdClass::class);
        $newRequest = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult->expects($this->once())
                    ->method('getMatchedRouteName')
                    ->willReturn($matchedRouteName);

        $requestBodyStream = new Stream('php://temp', 'wb+');
        $requestBodyStream->write($requestBody);
        $requestBodyStream->rewind();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo(RouteResult::class))
                ->willReturn($routeResult);
        $request->expects($this->once())
                ->method('getHeaderLine')
                ->with($this->identicalTo('Content-Type'))
                ->willReturn('application/json');
        $request->expects($this->once())
                ->method('getBody')
                ->willReturn($requestBodyStream);
        $request->expects($this->once())
                ->method('withParsedBody')
                ->with($this->identicalTo($parsedBody))
                ->willReturn($newRequest);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($newRequest))
                ->willReturn($response);

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())
                   ->method('deserialize')
                   ->with(
                       $this->identicalTo($requestBody),
                       $this->identicalTo($requestClass),
                       $this->identicalTo('json'),
                   )
                   ->willReturn($parsedBody);

        $instance = new RequestDeserializerMiddleware($serializer, $requestClassesByRoutes);
        $result = $instance->process($request, $handler);

        $this->assertSame($response, $result);
    }

    /**
     * @throws ServerException
     */
    public function testProcessWithInvalidRequestBody(): void
    {
        $matchedRouteName = 'abc';
        $requestClass = 'stdClass';
        $requestClassesByRoutes = [
            'abc' => 'stdClass',
        ];
        $requestBody = 'def';

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult->expects($this->once())
                    ->method('getMatchedRouteName')
                    ->willReturn($matchedRouteName);

        $requestBodyStream = new Stream('php://temp', 'wb+');
        $requestBodyStream->write($requestBody);
        $requestBodyStream->rewind();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo(RouteResult::class))
                ->willReturn($routeResult);
        $request->expects($this->once())
                ->method('getHeaderLine')
                ->with($this->identicalTo('Content-Type'))
                ->willReturn('application/json');
        $request->expects($this->once())
                ->method('getBody')
                ->willReturn($requestBodyStream);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())
                ->method('handle');

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())
                   ->method('deserialize')
                   ->with(
                       $this->identicalTo($requestBody),
                       $this->identicalTo($requestClass),
                       $this->identicalTo('json'),
                   )
                   ->willThrowException($this->createMock(Exception::class));

        $this->expectException(InvalidRequestBodyException::class);

        $instance = new RequestDeserializerMiddleware($serializer, $requestClassesByRoutes);
        $instance->process($request, $handler);
    }

    /**
     * @throws ServerException
     */
    public function testProcessWithoutRequestClass(): void
    {
        $matchedRouteName = 'abc';
        $requestClassesByRoutes = [];
        $response = $this->createMock(ResponseInterface::class);

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult->expects($this->once())
                    ->method('getMatchedRouteName')
                    ->willReturn($matchedRouteName);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo(RouteResult::class))
                ->willReturn($routeResult);
        $request->expects($this->once())
                ->method('getHeaderLine')
                ->with($this->identicalTo('Content-Type'))
                ->willReturn('application/json');
        $request->expects($this->never())
                ->method('getBody');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($request))
                ->willReturn($response);

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->never())
                   ->method('deserialize');

        $instance = new RequestDeserializerMiddleware($serializer, $requestClassesByRoutes);
        $result = $instance->process($request, $handler);

        $this->assertSame($response, $result);
    }
}
