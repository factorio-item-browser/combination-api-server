<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidRequestBodyException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the InvalidRequestBodyException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\CombinationApi\Server\Exception\InvalidRequestBodyException
 */
class InvalidRequestBodyExceptionTest extends TestCase
{
    public function test(): void
    {
        $message = 'abc';
        $previous = $this->createMock(Throwable::class);

        $exception = new InvalidRequestBodyException($message, $previous);

        $this->assertSame('Invalid request body: abc', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
