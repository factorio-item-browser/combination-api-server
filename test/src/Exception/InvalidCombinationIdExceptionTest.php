<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidCombinationIdException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the InvalidCombinationIdException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class InvalidCombinationIdExceptionTest extends TestCase
{
    public function test(): void
    {
        $combinationId = 'abc';
        $previous = $this->createMock(Throwable::class);

        $exception = new InvalidCombinationIdException($combinationId, $previous);

        $this->assertSame('Invalid combination id: abc', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
