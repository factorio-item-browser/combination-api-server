<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Exception;

use FactorioItemBrowser\CombinationApi\Server\Constant\HeaderName;
use Throwable;

/**
 * The exception thrown when none of the combination identifying headers were present.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class MissingCombinationHeaderException extends ServerException
{
    private const MESSAGE = 'Missing combination in request header: Must include one of %s.';
    private const HEADERS = [
        HeaderName::COMBINATION_ID,
        HeaderName::SHORT_COMBINATION_ID,
        HeaderName::MOD_NAMES,
    ];

    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, implode(', ', self::HEADERS)), 400, $previous);
    }
}
