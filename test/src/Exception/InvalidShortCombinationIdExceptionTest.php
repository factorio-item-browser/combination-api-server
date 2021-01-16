<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidShortCombinationIdException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the InvalidShortCombinationIdException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Exception\InvalidShortCombinationIdException
 */
class InvalidShortCombinationIdExceptionTest extends TestCase
{
    public function test(): void
    {
        $shortCombinationId = 'abc';
        $previous = $this->createMock(Throwable::class);

        $exception = new InvalidShortCombinationIdException($shortCombinationId, $previous);

        $this->assertSame('Invalid short combination id: abc', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
