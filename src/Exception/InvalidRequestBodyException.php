<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Exception;

use Throwable;

/**
 * The exception thrown when an invalid request body has been encountered.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class InvalidRequestBodyException extends ServerException
{
    private const MESSAGE = 'Invalid request body: %s';

    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $message), 400, $previous);
    }
}
