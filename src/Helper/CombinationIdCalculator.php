<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Helper;

use Exception;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidCombinationIdException;
use FactorioItemBrowser\CombinationApi\Server\Exception\InvalidShortCombinationIdException;
use FactorioItemBrowser\CombinationApi\Server\Exception\ServerException;
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
    private readonly Base62 $base62;

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
     * @throws ServerException
     */
    public function fromId(string $combinationId): UuidInterface
    {
        try {
            return Uuid::fromString($combinationId);
        } catch (Exception $e) {
            throw new InvalidCombinationIdException($combinationId, $e);
        }
    }

    /**
     * Creates the combination id from its short string representation.
     * @param string $shortCombinationId
     * @return UuidInterface
     * @throws ServerException
     */
    public function fromShortId(string $shortCombinationId): UuidInterface
    {
        try {
            $decodedId = $this->base62->decode($shortCombinationId);
            return Uuid::fromBytes(substr(str_pad($decodedId, 16, "\0", STR_PAD_LEFT), -16));
        } catch (Exception $e) {
            throw new InvalidShortCombinationIdException($shortCombinationId);
        }
    }

    /**
     * Transforms the combination id to its short string representation.
     * @param UuidInterface $combinationId
     * @return string
     */
    public function toShortId(UuidInterface $combinationId): string
    {
        $shortId = $this->base62->encode($combinationId->getBytes());
        return substr(str_pad($shortId, 22, '0', STR_PAD_LEFT), -22);
    }
}
