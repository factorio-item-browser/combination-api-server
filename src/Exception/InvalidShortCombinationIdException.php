<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Exception;

use Throwable;

/**
 * The exception thrown when a (syntactically) invalid short combination id is encountered.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class InvalidShortCombinationIdException extends ServerException
{
    private const MESSAGE = 'Invalid short combination id: %s';

    public function __construct(string $combinationId, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $combinationId), 400, $previous);
    }
}
