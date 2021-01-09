<?php

/**
 * The configuration of the Doctrine integration.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Ramsey\Uuid\Doctrine\UuidBinaryType;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'doctrine_mapping_types' => [
                    UuidBinaryType::NAME => Types::BINARY,
                    'enum' => 'string',
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => SimplifiedXmlDriver::class,
                'cache' => 'array',
                'paths' => [
                    'config/doctrine' => 'FactorioItemBrowser\CombinationApi\Server\Entity',
                ],
            ],
        ],
        'migrations' => [
            'orm_default' => [
                'directory' => 'data/migrations',
                'name'      => 'FactorioItemBrowser Combination API Migrations',
                'namespace' => 'FactorioItemBrowser\CombinationApi\Server\Migrations',
                'table'     => '_Migrations',
            ],
        ],
        'types' => [
            Doctrine\Type\JobStatusType::NAME => Doctrine\Type\JobStatusType::class,
            UuidBinaryType::NAME => UuidBinaryType::class,
        ],
    ],
];
