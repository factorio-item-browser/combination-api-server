<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\ActionNotAllowedException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the ActionNotAllowedException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\CombinationApi\Server\Exception\ActionNotAllowedException
 */
class ActionNotAllowedExceptionTest extends TestCase
{
    public function test(): void
    {
        $previous = $this->createMock(Throwable::class);

        $exception = new ActionNotAllowedException($previous);

        $this->assertSame('The requested action is not allowed.', $exception->getMessage());
        $this->assertSame(403, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
