<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\FailedModPortalRequestException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the FailedModPortalRequestException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Exception\FailedModPortalRequestException
 */
class FailedModPortalRequestExceptionTest extends TestCase
{
    public function test(): void
    {
        $message = 'abc';
        $previous = $this->createMock(Throwable::class);

        $exception = new FailedModPortalRequestException($message, $previous);

        $this->assertSame('Request to the Factorio Mod Portal failed: abc', $exception->getMessage());
        $this->assertSame(503, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
