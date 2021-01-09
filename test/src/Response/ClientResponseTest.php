<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Response;

use FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * The PHPUnit test of the ClientResponse class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Response\ClientResponse
 */
class ClientResponseTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::withSerializer
     */
    public function testWithSerializer(): void
    {
        $payload = new stdClass();
        $payload->foo = 'bar';
        $statusCode = 512;
        $headers = ['abc' => 'def'];
        $body = 'ghi';

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())
                   ->method('serialize')
                   ->with($this->identicalTo($payload), $this->identicalTo('json'))
                   ->willReturn($body);

        $instance = new ClientResponse($payload, $statusCode, $headers);
        $result = $instance->withSerializer($serializer);

        $this->assertSame($statusCode, $result->getStatusCode());
        $this->assertSame('def', $result->getHeaderLine('abc'));
        $this->assertSame('application/json', $result->getHeaderLine('Content-Type'));
        $this->assertSame($body, $result->getBody()->getContents());
    }
}
