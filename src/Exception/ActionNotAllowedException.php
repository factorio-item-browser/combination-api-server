<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Exception;

use Throwable;

/**
 * The exception thrown when an agent does not have sufficient permissions for an action.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ActionNotAllowedException extends ServerException
{
    private const MESSAGE = 'The requested action is not allowed.';

    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, 403, $previous);
    }
}
