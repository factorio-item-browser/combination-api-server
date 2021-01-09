<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * The type representing an enumeration of values.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractEnumType extends Type
{
    public const NAME = 'enum';
    public const VALUES = ['foo', 'bar'];

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $quotedValues = implode(',', array_map(function (string $value) use ($platform): string {
            return $platform->quoteStringLiteral(trim($value));
        }, static::VALUES));

        return sprintf('ENUM(%s)', $quotedValues);
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
