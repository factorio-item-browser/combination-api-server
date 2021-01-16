<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\UnknownJobException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Throwable;

/**
 * The PHPUnit test of the UnknownJobException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Exception\UnknownJobException
 */
class UnknownJobExceptionTest extends TestCase
{
    public function test(): void
    {
        $jobId = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $previous = $this->createMock(Throwable::class);

        $exception = new UnknownJobException($jobId, $previous);

        $this->assertSame('Job with id 01234567-89ab-cdef-0123-456789abcdef is not known.', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
