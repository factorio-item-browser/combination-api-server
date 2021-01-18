<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidApiKeyException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the InvalidApiKeyException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Exception\InvalidApiKeyException
 */
class InvalidApiKeyExceptionTest extends TestCase
{
    public function test(): void
    {
        $previous = $this->createMock(Throwable::class);

        $exception = new InvalidApiKeyException($previous);

        $this->assertSame('Invalid or missing API key.', $exception->getMessage());
        $this->assertSame(401, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
