<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Exception\UnknownCombinationException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Throwable;

/**
 * The PHPUnit test of the UnknownCombinationException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class UnknownCombinationExceptionTest extends TestCase
{
    public function test(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $previous = $this->createMock(Throwable::class);

        $exception = new UnknownCombinationException($combinationId, $previous);

        $this->assertSame(
            'Combination with id 2f4a45fa-a509-a9d1-aae6-ffcf984a7a76 (short: 1reA6H5z4uFpotvegbLIr4) is not known.',
            $exception->getMessage(),
        );
        $this->assertSame(404, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
