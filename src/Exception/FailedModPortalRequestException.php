<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Exception;

use Throwable;

/**
 * The exception when a request to the mod portal has failed. This excludes not found exceptions.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class FailedModPortalRequestException extends ServerException
{
    private const MESSAGE = 'Request to the Factorio Mod Portal failed: %s';

    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $message), 503, $previous);
    }
}
