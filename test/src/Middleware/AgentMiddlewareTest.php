<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Middleware;

use FactorioItemBrowser\CombinationApi\Client\Constant\HeaderName;
use FactorioItemBrowser\CombinationApi\Server\Entity\Agent;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidApiKeyException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
use FactorioItemBrowser\CombinationApi\Server\Middleware\AgentMiddleware;
use FactorioItemBrowser\CombinationApi\Server\Repository\AgentRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The PHPUnit test of the AgentMiddleware class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Middleware\AgentMiddleware
 */
class AgentMiddlewareTest extends TestCase
{
    /**
     * @throws ServerException
     */
    public function testProcess(): void
    {
        $apiKey = 'abc';
        $agent = $this->createMock(Agent::class);
        $response = $this->createMock(ResponseInterface::class);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->any())
                ->method('getHeaderLine')
                ->with($this->identicalTo(HeaderName::API_KEY))
                ->willReturn($apiKey);

        $agentRepository = $this->createMock(AgentRepository::class);
        $agentRepository->expects($this->once())
                        ->method('findByApiKey')
                        ->with($this->identicalTo($apiKey))
                        ->willReturn($agent);
        $agentRepository->expects($this->once())
                        ->method('setCurrentAgent')
                        ->with($this->identicalTo($agent));

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($request))
                ->willReturn($response);

        $instance = new AgentMiddleware($agentRepository);
        $result = $instance->process($request, $handler);

        $this->assertSame($response, $result);
    }

    /**
     * @throws ServerException
     */
    public function testProcessWithInvalidApiKey(): void
    {
        $apiKey = 'abc';

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->any())
                ->method('getHeaderLine')
                ->with($this->identicalTo(HeaderName::API_KEY))
                ->willReturn($apiKey);

        $agentRepository = $this->createMock(AgentRepository::class);
        $agentRepository->expects($this->once())
                        ->method('findByApiKey')
                        ->with($this->identicalTo($apiKey))
                        ->willReturn(null);
        $agentRepository->expects($this->never())
                        ->method('setCurrentAgent');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())
                ->method('handle');

        $this->expectException(InvalidApiKeyException::class);

        $instance = new AgentMiddleware($agentRepository);
        $instance->process($request, $handler);
    }
}
