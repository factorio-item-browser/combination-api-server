<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Helper\CombinationIdCalculator;
use Ramsey\Uuid\UuidInterface;
use Throwable;

/**
 * The exception thrown when a combination is not known to the server.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class UnknownCombinationException extends ServerException
{
    private const MESSAGE = 'Combination with id %s (short: %s) is not known.';

    public function __construct(UuidInterface $combinationId, ?Throwable $previous = null)
    {
        $shortId = (new CombinationIdCalculator())->toShortId($combinationId);
        $message = sprintf(self::MESSAGE, $combinationId->toString(), $shortId);
        parent::__construct($message, 404, $previous);
    }
}
