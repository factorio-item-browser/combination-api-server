<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidJobIdException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the InvalidJobIdException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Exception\InvalidJobIdException
 */
class InvalidJobIdExceptionTest extends TestCase
{
    public function test(): void
    {
        $jobId = 'abc';
        $previous = $this->createMock(Throwable::class);

        $exception = new InvalidJobIdException($jobId, $previous);

        $this->assertSame('Invalid job id: abc', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
