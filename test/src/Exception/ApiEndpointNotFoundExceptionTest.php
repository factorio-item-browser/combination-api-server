<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\ApiEndpointNotFoundException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the ApiEndpointNotFoundException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ApiEndpointNotFoundExceptionTest extends TestCase
{
    public function test(): void
    {
        $endpoint = 'abc';
        $previous = $this->createMock(Throwable::class);

        $exception = new ApiEndpointNotFoundException($endpoint, $previous);

        $this->assertSame('API endpoint not found: abc', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
