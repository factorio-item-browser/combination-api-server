<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\MissingCombinationHeaderException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the MissingCombinationHeaderException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Exception\MissingCombinationHeaderException
 */
class MissingCombinationHeaderExceptionTest extends TestCase
{
    public function test(): void
    {
        $previous = $this->createMock(Throwable::class);

        $exception = new MissingCombinationHeaderException($previous);

        $this->assertSame(
            'Missing combination in request header: '
                . 'Must include one of Combination-Id, Short-Combination-Id, Mod-Names.',
            $exception->getMessage(),
        );
        $this->assertSame(400, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
