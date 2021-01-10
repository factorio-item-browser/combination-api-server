<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Helper;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Tuupola\Base62;

/**
 * The class helping with calculating and transforming combination ids.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CombinationIdCalculator
{
    private Base62 $base62;

    public function __construct()
    {
        $this->base62 = new Base62(['characters' => Base62::INVERTED]);
    }

    /**
     * Calculates the combination id from the mod names.
     * @param array<string> $modNames
     * @return UuidInterface
     */
    public function fromModNames(array $modNames): UuidInterface
    {
        $modNames = array_map(fn(string $modName): string => rtrim($modName), $modNames);
        sort($modNames);
        return Uuid::fromString(hash('md5', (string) json_encode($modNames)));
    }

    /**
     * Creates the combination id from its full string representation.
     * @param string $combinationId
     * @return UuidInterface
     */
    public function fromId(string $combinationId): UuidInterface
    {
        return Uuid::fromString($combinationId);
    }

    /**
     * Creates the combination id from its short string representation.
     * @param string $shortCombinationId
     * @return UuidInterface
     */
    public function fromShortId(string $shortCombinationId): UuidInterface
    {
        $base62 = new Base62(['characters' => Base62::INVERTED]);
        return Uuid::fromBytes(substr(str_pad($base62->decode($shortCombinationId), 16, "\0", STR_PAD_LEFT), -16));
    }

    /**
     * Transforms the combination id to its short string representation.
     * @param UuidInterface $combinationId
     * @return string
     */
    public function toShortId(UuidInterface $combinationId): string
    {
        $base62 = new Base62(['characters' => Base62::INVERTED]);
        $shortId = $base62->encode($combinationId->getBytes());
        return substr(str_pad($shortId, 22, '0', STR_PAD_LEFT), -22);
    }
}
