<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Exception;

use Throwable;

/**
 * The exception thrown when a (syntactically) invalid job id was encountered.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class InvalidJobIdException extends ServerException
{
    private const MESSAGE = 'Invalid job id: %s';

    public function __construct(string $jobId, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $jobId), 400, $previous);
    }
}
