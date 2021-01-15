<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Exception;

use Ramsey\Uuid\UuidInterface;
use Throwable;

/**
 * The exception thrown when a job is not known to the server.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class UnknownJobException extends ServerException
{
    private const MESSAGE = 'Job with id %s is not known.';

    public function __construct(UuidInterface $jobId, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $jobId->toString()), 404, $previous);
    }
}
